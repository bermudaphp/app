<?php

namespace Bermuda\App\Console;

abstract class QuestionBuilder
{
    protected ?iterable $variants = null;
    protected int|string|bool|null $defaultValue = null;
    protected ?bool $isHidden = null;
    protected ?bool $confirm = null;
    protected ?bool $multiSelect = null;
    protected ?string $pattern = null;
    protected string $question = '';
    protected ?array $autocomplete = null;
    protected ?string $errorMessage = null;
    protected ?int $maxAttempts = null;

    /**
     * @var callable|null
     */
    protected $normalizer = null, $validator = null;

    /**
     * @param int|string|bool|null $value
     * @return $this
     */
    public function setDefaultValue(int|string|bool|null $value): self
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * @param int|null $num
     * @return $this
     */
    public function setMaxAttempts(?int $num): self
    {
        $this->maxAttempts = $num;
        return $this;
    }

    /**
     * @param array|null $values
     * @return $this
     */
    public function setAutocomplete(?array $values): self
    {
        $this->autocomplete = $values;
        return $this;
    }

    /**
     * @param bool $mode
     * @param string $pattern
     * @return $this
     */
    public function setConfirm(?bool $mode, string $pattern = '/^y/i'): self
    {
        $this->confirm = $mode;
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setErrorMessage(?string $message = null): self
    {
        $this->errorMessage = $message;
        return $this;
    }

    /**
     * @param string $question
     * @return $this
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @param array|null $variants
     * @return $this
     */
    public function setVariants(?array $variants): self
    {
        $this->variants = array_values($variants);
        return $this;
    }

    /**
     * @param bool|null $mode
     * @return $this
     */
    public function setHidden(?bool $mode): self
    {
        $this->isHidden = $mode;
        return $this;
    }

    /**
     * @param bool|null $mode
     * @return $this
     */
    public function setMultiSelect(?bool $mode): self
    {
        $this->multiSelect = $mode;
        return $this;
    }

    /**
     * @param callable|null $validator
     * @return $this
     */
    public function setValidator(?callable $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @param callable|null $normalizer
     * @return $this
     */
    public function setNormalizer(?callable $normalizer): self
    {
        $this->normalizer = $normalizer;
        return $this;
    }

    abstract public function build(): object;
}
