<?php

namespace App\Assistants\Elastic\ValueObjects\Search;

/**
 * Class Suggester
 *
 * @package App\Assistants\Elastic\ValueObjects\Search
 */
final class Suggester
{
    /**
     * @var string
     */
    private string $analyzer;

    /**
     * @var string
     */
    private string $field;

    /**
     * @var string
     */
    private string $term;

    /**
     * @param string $analyzer
     * @param string $field
     * @param string $term
     */
    public function __construct(string $analyzer, string $field, string $term)
    {
        $this->setAnalyzer($analyzer);
        $this->setField($field);
        $this->setTerm($term);
    }

    /**
     * @return string
     */
    public function analyzer(): string
    {
        return $this->analyzer;
    }

    /**
     * @param string $analyzer
     *
     * @return Suggester
     */
    private function setAnalyzer(string $analyzer): Suggester
    {
        $this->analyzer = $analyzer;

        return $this;
    }

    /**
     * @return string
     */
    public function field(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return Suggester
     */
    private function setField(string $field): Suggester
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function term(): string
    {
        return $this->term;
    }

    /**
     * @param string $term
     *
     * @return Suggester
     */
    private function setTerm(string $term): Suggester
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            $this->analyzer() => [
                'text' => $this->term(),
                'phrase' => [
                    'field' => $this->field(),
                ],
            ],
        ];
    }
}
