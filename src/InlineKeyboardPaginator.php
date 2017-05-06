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
     * @param int $max_buttons
     *
     * @return InlineKeyboardPagination
     */
    public function setMaxButtons(int $max_buttons = 5): InlineKeyboardPagination;

    /**
     * >#VALUE#<, <#VALUE#>, |#VALUE#| etc...
     *
     * @param string $wrap_selected_button
     *
     * @return InlineKeyboardPagination
     */
    public function setWrapSelectedButton(string $wrap_selected_button = '« #VALUE# »'): InlineKeyboardPagination;

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
     * InlineKeyboardPaginator constructor.
     *
     * @param array  $items
     * @param string $command
     * @param int    $selected_page
     * @param int    $limit
     */
    public function __construct(array $items, string $command, int $selected_page, int $limit);

    /**
     * @param int $selected_page
     *
     * @return array
     */
    public function paginate(int $selected_page = null): array;
}
