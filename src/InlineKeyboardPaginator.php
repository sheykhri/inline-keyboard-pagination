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
     * InlineKeyboardPaginator constructor.
     *
     * @param array  $items
     * @param string $command
     * @param int    $selected_page
     * @param int    $items_per_page
     */
    public function __construct(array $items, string $command, int $selected_page, int $items_per_page);

    /**
     * @param int $max_buttons
     *
     * @return InlineKeyboardPagination
     */
    public function setMaxButtons(int $max_buttons = 5): InlineKeyboardPagination;

    /**
     * @param string $command
     *
     * @return InlineKeyboardPagination
     */
    public function setCommand(string $command = 'pagination'): InlineKeyboardPagination;

    /**
     * @param int $selected_page
     *
     * @return InlineKeyboardPagination
     */
    public function setSelectedPage(int $selected_page): InlineKeyboardPagination;

    /**
     * @param int $selected_page
     *
     * @return array
     */
    public function getPagination(int $selected_page = null): array;
}
