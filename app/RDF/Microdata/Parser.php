<?php

namespace App\RDF\Microdata;


use App\RDF\Microdata\Middleware\TypeMiddleware;

class Parser
{
    private const EXTRACT_DIRECT_PROPS = 'descendant::*[@itemprop][not(@itemtype)][not(ancestor::*[@itemtype][1]/@itemtype[not(contains(., "%s"))])]';

    /**
     * @var TypeMiddleware[]
     */
    private array $typeMiddleware = [];

    public function registerMiddleware(TypeMiddleware $middleware): void
    {
        $this->typeMiddleware[] = $middleware;
    }

    public function parse(string $html): array
    {
        $dom = new \DOMDocument();

        \libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $ctx = new Context($dom, $xpath);
        $superTypes = $ctx->xpath->query("//*[@itemtype][not(ancestor::*[@itemtype])]");

        $result = [];

        foreach (self::filterElements($superTypes) as $superType) {
            $result[] = $this->parseType($ctx->withElement($superType));
        }

        return $result;
    }

    public function parseType(Context $ctx): Type
    {
        $resultType = [];

        if ($ctx->element->hasAttribute('content')) {
            $resultType['content'] = $ctx->element->getAttribute('content');
        }

        $subTypes = self::filterElements($ctx->xpath->query('descendant::*[@itemtype]', $ctx->element));

        foreach ($subTypes as $subType) {
            // TODO: co tohle znamená
            if (!$subType->hasAttribute('itemprop')) {
                continue;
            }

            $subTypeResult = null;
            foreach ($this->typeMiddleware as $middleware) {
                if ($middleware->check($ctx->withElement($subTypeResult ?? $subType))) {
                    $subTypeResult = $middleware->run($ctx->withElement($subType));
                }
            }

            if ($subTypeResult !== null) {
                $resultType[$subType->getAttribute('itemprop')] = $subTypeResult;

                continue;
            }

            $resultType[$subType->getAttribute('itemprop')] = $this->parseType($ctx->withElement($subType));
        }

        $rootProps = self::filterElements($ctx->xpath->query(
            sprintf(self::EXTRACT_DIRECT_PROPS, $ctx->element->getAttribute('itemtype')),
            $ctx->element,
        ));

        foreach ($rootProps as $prop) {
            $propName = $prop->getAttribute('itemprop');

            if ($prop->hasAttribute('content')) {
                $resultType[$propName] = $prop->getAttribute('content');

                continue;
            }

            // teoreticky špatně, ale aspoň chci nějaký info
            if ($prop->tagName === 'img') {
                $resultType[$propName] = $prop->getAttribute('src');

                continue;
            }

            if ($prop->tagName === 'a' && preg_match('/.*(url)|(uri)/',$prop->getAttribute('itemprop'))) {
                $resultType[$propName] = $prop->getAttribute('href');

                continue;
            }

            $resultType[$propName] = trim($prop->textContent);
        }

        return new Type($ctx->element->getAttribute('itemtype'), $resultType);
    }

    public static function isType(\DOMElement $element): bool
    {
        return $element->hasAttribute('itemtype');
    }

    public static function isElement(\DOMNode $node): bool
    {
        return $node instanceof \DOMElement;
    }

    /**
     * @param array|\DOMNodeList $nodeList
     * @return \DOMElement[]
     */
    public static function filterElements(array|\DOMNodeList $nodeList): array
    {
        $resultList = [];
        foreach ($nodeList as $node) {
            if (!self::isElement($node)) {
                continue;
            }

            $resultList[] = $node;
        }

        return $resultList;
    }
}
