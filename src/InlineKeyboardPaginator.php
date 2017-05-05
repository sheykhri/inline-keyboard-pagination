<?php

namespace TelegramBot\InlineKeyboardPagination;

/**
 * Interface InlineKeyboardPaginator
 *
 * @package TelegramBot\InlineKeyboardPagination
 */
interface InlineKeyboardPaginator
{
    /**
     * @param int $maxButtons
     *
     * @return InlineKeyboardPagination
     */
    public function setMaxButtons(int $maxButtons = 5): InlineKeyboardPagination;

    /**
     * >#VALUE#<, <#VALUE#>, |#VALUE#| etc...
     *
     * @param string $wrapSelectedButton
     *
     * @return InlineKeyboardPagination
     */
    public function setWrapSelectedButton(string $wrapSelectedButton = '« #VALUE# »'): InlineKeyboardPagination;

    /**
     * @param string $command
     *
     * @return InlineKeyboardPagination
     */
    public function setCommand(string $command = 'pagination'): InlineKeyboardPagination;

    /**
     * @param int $selectedPage
     *
     * @return InlineKeyboardPagination
     */
    public function setSelectedPage(int $selectedPage): InlineKeyboardPagination;

    /**
     * InlineKeyboardPaginator constructor.
     *
     * @param array  $items
     * @param string $command
     * @param int    $selectedPage
     * @param int    $limit
     */
    public function __construct(array $items, string $command, int $selectedPage, int $limit);

    /**
     * @param int $selectedPage
     *
     * @return array
     */
    public function paginate(int $selectedPage = null): array;
}
