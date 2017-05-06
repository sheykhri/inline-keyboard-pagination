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
    private $limit;

    /**
     * @var integer
     */
    private $max_buttons = 5;

    /**
     * @var integer
     */
    private $first_page = 1;

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
     * @var string
     */
    private $wrap_selected_button = '« #VALUE# »';

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setMaxButtons(int $max_buttons = 5): InlineKeyboardPagination
    {
        if ($max_buttons < 5 || $max_buttons > 8) {
            throw new InlineKeyboardPaginationException('Invalid max buttons');
        }
        $this->max_buttons = $max_buttons;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setWrapSelectedButton(string $wrap_selected_button = '« #VALUE# »'): InlineKeyboardPagination
    {
        if (false === strpos($wrap_selected_button, '/#VALUE#/')) {
            throw new InlineKeyboardPaginationException('Invalid selected button wrapper');
        }
        $this->wrap_selected_button = $wrap_selected_button;

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
            throw new InlineKeyboardPaginationException('Invalid selected page');
        }
        $this->selected_page = $selected_page;

        return $this;
    }

    /**
     * TelegramBotPagination constructor.
     *
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function __construct(array $items, string $command = 'pagination', int $selected_page = 1, int $limit = 3)
    {
        $this->number_of_pages = $this->countTheNumberOfPage(count($items), $limit);

        $this->setSelectedPage($selected_page);

        $this->items   = $items;
        $this->limit   = $limit;
        $this->command = $command;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function paginate(int $selected_page = null): array
    {
        if ($selected_page !== null) {
            $this->setSelectedPage($selected_page);
        }

        return [
            'items'    => $this->getPreparedItems(),
            'keyboard' => $this->generateKeyboard(),
        ];
    }

    /**
     * @return array
     */
    protected function generateKeyboard(): array
    {
        $buttons = [];

        if ($this->number_of_pages > $this->max_buttons) {
            $buttons[] = $this->generateButton($this->first_page);

            $range = $this->generateRange();

            for ($i = $range['from']; $i < $range['to']; $i++) {
                $buttons[] = $this->generateButton($i);
            }

            $buttons[] = $this->generateButton($this->number_of_pages);
        } else {
            for ($i = 1; $i <= $this->number_of_pages; $i++) {
                $buttons[] = $this->generateButton($i);
            }
        }

        return $buttons;
    }

    /**
     * @return array
     */
    protected function generateRange(): array
    {
        $number_of_intermediate_buttons = $this->max_buttons - 2;

        if ($this->selected_page === $this->first_page) {
            $from = 2;
            $to   = $from + $number_of_intermediate_buttons;
        } elseif ($this->selected_page === $this->number_of_pages) {
            $from = $this->number_of_pages - $number_of_intermediate_buttons;
            $to   = $this->number_of_pages;
        } else {
            if (($this->selected_page + $number_of_intermediate_buttons) > $this->number_of_pages) {
                $from = $this->number_of_pages - $number_of_intermediate_buttons;
                $to   = $this->number_of_pages;
            } elseif (($this->selected_page - 2) < $this->first_page) {
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
        $label        = "$next_page";
        $callbackData = $this->generateCallbackData($next_page);

        if ($next_page === $this->selected_page) {
            $label = str_replace('#VALUE#', $label, $this->wrap_selected_button);
        }

        return [
            'text'          => $label,
            'callback_data' => $callbackData,
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
        return array_slice($this->items, $this->getOffset(), $this->limit);
    }

    /**
     * @return int
     */
    protected function getOffset(): int
    {
        return $this->limit * ($this->selected_page - 1);
    }

    /**
     * @param $items_length
     * @param $limit
     *
     * @return int
     */
    protected function countTheNumberOfPage($items_length, $limit): int
    {
        return (int) ceil($items_length / $limit);
    }
}
