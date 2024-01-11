<?php

namespace App\RDF\Microdata;

use JetBrains\PhpStorm\Language;

readonly class Context
{
    public function __construct(
        public \DOMDocument $dom,
        public \DOMXPath $xpath,
        public ?\DOMElement $element = null,
    ) {
    }

    public function withElement(\DOMElement $element): self
    {
        return new self($this->dom, $this->xpath, $element);
    }

    /**
     * @return \DOMElement[]|null
     */
    public function query(#[Language('XPath')] string $expression): ?array
    {
        if (($result = $this->xpath->query($expression, $this->element)) instanceof \DOMNodeList) {
            return Parser::filterElements($result);
        }

        return null;
    }
}
