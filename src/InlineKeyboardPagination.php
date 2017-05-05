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
    private $maxButtons = 5;

    /**
     * @var integer
     */
    private $firstPage = 1;

    /**
     * @var integer
     */
    private $selectedPage;

    /**
     * @var integer
     */
    private $numberOfPages;

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
    private $wrapSelectedButton = '« #VALUE# »';

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setMaxButtons(int $maxButtons = 5): InlineKeyboardPagination
    {
        if ($maxButtons < 5 || $maxButtons > 8) {
            throw new InlineKeyboardPaginationException('Invalid max buttons');
        }
        $this->maxButtons = $maxButtons;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function setWrapSelectedButton(string $wrapSelectedButton = '« #VALUE# »'): InlineKeyboardPagination
    {
        if (false === strpos($wrapSelectedButton, '/#VALUE#/')) {
            throw new InlineKeyboardPaginationException('Invalid selected button wrapper');
        }
        $this->wrapSelectedButton = $wrapSelectedButton;
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
    public function setSelectedPage(int $selectedPage): InlineKeyboardPagination
    {
        if ($selectedPage < 1 || $selectedPage > $this->numberOfPages) {
            throw new InlineKeyboardPaginationException('Invalid selected page');
        }
        $this->selectedPage = $selectedPage;

        return $this;
    }

    /**
     * TelegramBotPagination constructor.
     *
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function __construct(array $items, string $command = 'pagination', int $selectedPage = 1, int $limit = 3)
    {
        $this->numberOfPages = $this->countTheNumberOfPage(count($items), $limit);

        $this->setSelectedPage($selectedPage);

        $this->items   = $items;
        $this->limit   = $limit;
        $this->command = $command;
    }

    /**
     * @inheritdoc
     * @throws InlineKeyboardPaginationException
     */
    public function paginate(int $selectedPage = null): array
    {
        if ($selectedPage !== null) {
            $this->setSelectedPage($selectedPage);
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

        if ($this->numberOfPages > $this->maxButtons) {

            $buttons[] = $this->generateButton($this->firstPage);

            $range = $this->generateRange();

            for ($i = $range['from']; $i < $range['to']; $i++) {
                $buttons[] = $this->generateButton($i);
            }

            $buttons[] = $this->generateButton($this->numberOfPages);

        } else {
            for ($i = 1; $i <= $this->numberOfPages; $i++) {
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
        $numberOfIntermediateButtons = $this->maxButtons - 2;

        if ($this->selectedPage == $this->firstPage) {
            $from = 2;
            $to   = $from + $numberOfIntermediateButtons;
        } elseif ($this->selectedPage == $this->numberOfPages) {
            $from = $this->numberOfPages - $numberOfIntermediateButtons;
            $to   = $this->numberOfPages;
        } else {
            if (($this->selectedPage + $numberOfIntermediateButtons) > $this->numberOfPages) {
                $from = $this->numberOfPages - $numberOfIntermediateButtons;
                $to   = $this->numberOfPages;
            } elseif (($this->selectedPage - 2) < $this->firstPage) {
                $from = $this->selectedPage;
                $to   = $this->selectedPage + $numberOfIntermediateButtons;
            } else {
                $from = $this->selectedPage - 1;
                $to   = $this->selectedPage + 2;
            }
        }
        return compact('from', 'to');
    }

    /**
     * @param int $nextPage
     *
     * @return array
     */
    protected function generateButton(int $nextPage): array
    {
        $label        = "$nextPage";
        $callbackData = $this->generateCallbackData($nextPage);

        if ($nextPage === $this->selectedPage) {
            $label = str_replace('#VALUE#', $label, $this->wrapSelectedButton);
        }
        return [
            'text'          => $label,
            'callback_data' => $callbackData,
        ];
    }

    /**
     * @param int $nextPage
     *
     * @return string
     */
    protected function generateCallbackData(int $nextPage): string
    {
        return "$this->command?currentPage=$this->selectedPage&nextPage=$nextPage";
    }

    /**
     * @return array
     */
    protected function getPreparedItems(): array
    {
        $offset = $this->getOffset();

        return array_slice($this->items, $offset, $this->limit);
    }

    /**
     * @return int
     */
    protected function getOffset(): int
    {
        return ($this->limit * $this->selectedPage) - $this->limit;
    }

    /**
     * @param $itemsLength
     * @param $limit
     *
     * @return int
     */
    protected function countTheNumberOfPage($itemsLength, $limit): int
    {
        $numberOfPages = ceil($itemsLength / $limit);

        return (int) $numberOfPages;
    }
}
