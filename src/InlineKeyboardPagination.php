<?php

namespace TelegramBot\InlineKeyboardPagination;

use TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException;

/**
 * Class InlineKeyboardPagination
 *
 * @package TelegramBot\InlineKeyboardPagination
 */
class InlineKeyboardPagination implements InlineKeyboardPaginator
{
    /**
     * @var integer
     */
    private $items_per_page;

    /**
     * @var integer
     */
    private $max_buttons = 5;

    /**
     * @var integer
     */
    private $selected_page;

    /**
     * @var integer
     */
    private $number_of_pages;

    /**
     * @var array
     */
    private $items;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $labels = [
        'default'  => '%d',
        'first'    => '« %d',
        'previous' => '‹ %d',
        'current'  => '· %d ·',
        'next'     => '%d ›',
        'last'     => '%d »',
    ];

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setMaxButtons(int $max_buttons = 5): InlineKeyboardPagination
    {
        if ($max_buttons < 5 || $max_buttons > 8) {
            throw new InlineKeyboardPaginationException('Invalid max buttons, must be between 5 and 8.');
        }
        $this->max_buttons = $max_buttons;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     *
     * @return InlineKeyboardPagination
     */
    public function setLabels($labels): InlineKeyboardPagination
    {
        $this->labels = array_merge($this->labels, $labels);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCommand(string $command = 'pagination'): InlineKeyboardPagination
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setSelectedPage(int $selected_page): InlineKeyboardPagination
    {
        if ($selected_page < 1 || $selected_page > $this->number_of_pages) {
            throw new InlineKeyboardPaginationException('Invalid selected page, must be between 1 and ' . $this->number_of_pages);
        }
        $this->selected_page = $selected_page;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->items_per_page;
    }

    /**
     * @param int $items_per_page
     *
     * @return InlineKeyboardPagination
     * @throws InlineKeyboardPaginationException
     */
    public function setItemsPerPage($items_per_page): InlineKeyboardPagination
    {
        if ($items_per_page <= 0) {
            throw new InlineKeyboardPaginationException('Invalid number of items per page, must be at least 1');
        }
        $this->items_per_page = $items_per_page;

        return $this;
    }

    /**
     * TelegramBotPagination constructor.
     *
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function __construct(array $items, string $command = 'pagination', int $selected_page = 1, int $items_per_page = 5)
    {
        $this->number_of_pages = $this->countTheNumberOfPage(count($items), $items_per_page);

        $this->setSelectedPage($selected_page);

        $this->items          = $items;
        $this->items_per_page = $items_per_page;
        $this->command        = $command;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function getPagination(int $selected_page = null): array
    {
        if ($selected_page !== null) {
            $this->setSelectedPage($selected_page);
        }

        return [
            'items'    => $this->getPreparedItems(),
            'keyboard' => [$this->generateKeyboard()],
        ];
    }

    /**
     * @return array
     */
    protected function generateKeyboard(): array
    {
        $buttons = [];

        if ($this->number_of_pages > $this->max_buttons) {
            $buttons[1] = $this->generateButton(1);

            $range = $this->generateRange();
            for ($i = $range['from']; $i < $range['to']; $i++) {
                $buttons[$i] = $this->generateButton($i);
            }

            $buttons[$this->number_of_pages] = $this->generateButton($this->number_of_pages);
        } else {
            for ($i = 1; $i <= $this->number_of_pages; $i++) {
                $buttons[$i] = $this->generateButton($i);
            }
        }

        // Set the correct labels.
        foreach ($buttons as $page => &$button) {
            $in_first_block = $this->selected_page <= 3 && $page <= 3;
            $in_last_block  = $this->selected_page >= $this->number_of_pages - 2 && $page >= $this->number_of_pages - 2;

            $label_key = 'next';
            if ($page === $this->selected_page) {
                $label_key = 'current';
            } elseif ($in_first_block || $in_last_block) {
                $label_key = 'default';
            } elseif ($page === 1) {
                $label_key = 'first';
            } elseif ($page === $this->number_of_pages) {
                $label_key = 'last';
            } elseif ($page < $this->selected_page) {
                $label_key = 'previous';
            }

            $label = $this->labels[$label_key];

            if ($label === '' || $label === null) {
                $button = null;
                continue;
            }

            $button['text'] = sprintf($label, $page);
        }

        return array_filter($buttons);
    }

    /**
     * @return array
     */
    protected function generateRange(): array
    {
        $number_of_intermediate_buttons = $this->max_buttons - 2;

        if ($this->selected_page === 1) {
            $from = 2;
            $to   = $from + $number_of_intermediate_buttons;
        } elseif ($this->selected_page === $this->number_of_pages) {
            $from = $this->number_of_pages - $number_of_intermediate_buttons;
            $to   = $this->number_of_pages;
        } else {
            if (($this->selected_page + $number_of_intermediate_buttons) > $this->number_of_pages) {
                $from = $this->number_of_pages - $number_of_intermediate_buttons;
                $to   = $this->number_of_pages;
            } elseif (($this->selected_page - 2) < 1) {
                $from = $this->selected_page;
                $to   = $this->selected_page + $number_of_intermediate_buttons;
            } else {
                $from = $this->selected_page - 1;
                $to   = $this->selected_page + 2;
            }
        }

        return compact('from', 'to');
    }

    /**
     * @param int $next_page
     *
     * @return array
     */
    protected function generateButton(int $next_page): array
    {
        return [
            'text'          => "$next_page",
            'callback_data' => $this->generateCallbackData($next_page),
        ];
    }

    /**
     * @param int $next_page
     *
     * @return string
     */
    protected function generateCallbackData(int $next_page): string
    {
        return "$this->command?currentPage=$this->selected_page&nextPage=$next_page";
    }

    /**
     * @return array
     */
    protected function getPreparedItems(): array
    {
        return array_slice($this->items, $this->getOffset(), $this->items_per_page);
    }

    /**
     * @return int
     */
    protected function getOffset(): int
    {
        return $this->items_per_page * ($this->selected_page - 1);
    }

    /**
     * @param $items_count
     * @param $items_per_page
     *
     * @return int
     */
    protected function countTheNumberOfPage($items_count, $items_per_page): int
    {
        return (int) ceil($items_count / $items_per_page);
    }
}
