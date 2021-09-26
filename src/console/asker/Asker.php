<?php

namespace Bermuda\App\Console\Asker;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

final class Asker
{
    private ?QuestionHelper $helper = null;
    public function __construct(private InputInterface $input, private OutputInterface $output)
    {
    }

    /**
     * @param string|callable $question
     * @param string|int|float|bool|null $default
     * @param array|null $autocomplete
     * @param bool|null $hidden
     * @return array|string|int|bool|float|null
     */
    public function ask(string|callable $question, string|int|float|bool $default = null, ?array $autocomplete = null, ?bool $hidden = null): array|string|int|bool|float|null
    {
        $question = $this->builder($question)
            ->setDefaultValue($default)
            ->setHidden($hidden)
            ->setAutocomplete($autocomplete)
            ->build();

        return $this->askQuestion($question);
    }

    /**
     * @param string|callable $question
     * @param string|int|float|bool|null $default
     * @param string $pattern
     * @return array|string|int|bool|float|null
     */
    public function askConfirm(string|callable $question, string|int|float|bool $default = null, string $pattern = '/^y/i'): array|string|int|bool|float|null
    {
        $question = $this->builder($question)
            ->setDefaultValue($default)
            ->setConfirm(true, $pattern)
            ->build();

        return $this->askQuestion($question);
    }

    /**
     * @param string|callable $question
     * @param array $variants
     * @param string|int|float|bool|null $default
     * @param string|null $errorMessage
     * @return array|string|int|bool|float|null
     */
    public function askMultiselect(string|callable $question, array $variants, string|int|float|bool $default = null, ?string $errorMessage = null): array|string|int|bool|float|null
    {
        return $this->askSelect($question, $variants, $default, $errorMessage, true);
    }

    /**
     * @param string|callable $question
     * @param array $variants
     * @param string|int|float|bool|null $default
     * @param string|null $errorMessage
     * @param bool $multiselect
     * @return array|string|int|bool|float|null
     */
    public function askSelect(string|callable $question, array $variants, string|int|float|bool $default = null, ?string $errorMessage = null, ?bool $multiselect = null): array|string|int|bool|float|null
    {
        $question = $this->builder($question)
            ->setVariants($variants)
            ->setDefaultValue($default)
            ->setErrorMessage($errorMessage)
            ->setMultiSelect($multiselect)
            ->build();

        return $this->askQuestion($question);
    }

    /**
     * @param Question $question
     * @return array|string|int|bool|float|null
     */
    private function askQuestion(Question $question): array|string|int|bool|float|null
    {
        if ($this->helper === null) {
            $this->helper = new QuestionHelper();
        }

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function builder(string|callable $question): QuestionBuilder
    {
        if (!is_callable($question)) {
            $question = static function(QuestionBuilder $qb) use ($question): void {
                $qb->setQuestion($question);
            };
        }

        $qb = new class extends QuestionBuilder
        {
            public function build(): Question
            {
                if ($this->variants != null) {
                    $question = new ChoiceQuestion($this->question, $this->variants, $this->defaultValue);
                } elseif ($this->confirm) {
                    $question = new ConfirmationQuestion($this->question, (bool) $this->defaultValue, $this->pattern);
                } else {
                    $question = new Question($this->question, $this->defaultValue);
                }

                if ($this->validator != null) {
                    $question->setValidator($this->validator);
                }

                if ($this->normalizer != null) {
                    $question->setNormalizer($this->normalizer);
                }

                if ($this->errorMessage !== null && $question instanceof ChoiceQuestion) {
                    $question->setErrorMessage($this->errorMessage);
                }

                if ($this->isHidden) {
                    $question->setHidden(true);
                    $question->setHiddenFallback(true);
                }

                if ($this->multiSelect && $question instanceof ChoiceQuestion) {
                    $question->setMultiselect(true);
                }

                if ($this->autocomplete) {
                    $question->setAutocompleterValues($this->autocomplete);
                }

                return $question;
            }
        };

        $question($qb);

        return $qb;
    }
}
