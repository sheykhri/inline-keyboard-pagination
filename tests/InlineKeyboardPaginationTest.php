<?php

namespace TelegramBot\InlineKeyboardPagination\Tests;

use TelegramBot\InlineKeyboardPagination\InlineKeyboardPagination;

/**
 * Class InlineKeyboardPaginationTest
 */
final class InlineKeyboardPaginationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $items_per_page = 5;

    /**
     * @var int
     */
    private $selected_page;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $items;

    /**
     * InlineKeyboardPaginationTest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->items         = range(1, 100);
        $this->command       = 'testCommand';
        $this->selected_page = random_int(1, 15);
    }

    public function testValidConstructor()
    {
        $ikp = new InlineKeyboardPagination($this->items, $this->command, $this->selected_page, $this->items_per_page);

        $data = $ikp->paginate();

        $this->assertCount($this->items_per_page, $data['items']);
        $this->assertArrayHasKey('keyboard', $data);
        $this->assertArrayHasKey(0, $data['keyboard']);
        $this->assertArrayHasKey('text', $data['keyboard'][0]);
        $this->assertStringStartsWith($this->command, $data['keyboard'][0]['callback_data']);
    }

    /**
     * @expectedException \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function testInvalidConstructor()
    {
        $ikp = new InlineKeyboardPagination($this->items, $this->command, 10000, $this->items_per_page);
        $ikp->paginate();
    }

    public function testValidPaginate()
    {
        $ikp = new InlineKeyboardPagination($this->items, $this->command, $this->selected_page, $this->items_per_page);

        $length = (int) ceil(count($this->items) / $this->items_per_page);

        for ($i = 1; $i < $length; $i++) {
            $ikp->paginate($i);
        }

        $this->assertTrue(true);
    }

    /**
     * @expectedException \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function testInvalidPaginate()
    {
        $ikp = new InlineKeyboardPagination($this->items, $this->command, $this->selected_page, $this->items_per_page);

        $length = (int) ceil(count($this->items) / $this->items_per_page) + 1;

        for ($i = $length; $i < $length * 2; $i++) {
            $ikp->paginate($i);
        }
    }

    /**
     * @expectedException \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function testInvalidMaxButtons()
    {
        $ikp = new InlineKeyboardPagination(range(1, 240));
        $ikp->setMaxButtons(2);
        $ikp->paginate();
    }

    /**
     * @expectedException \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function testInvalidSelectedPage()
    {
        $ikp = new InlineKeyboardPagination(range(1, 240));
        $ikp->setSelectedPage(-5);
        $ikp->paginate();
    }

    /**
     * @expectedException \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function testInvalidWrapSelectedButton()
    {
        $ikp = new InlineKeyboardPagination(range(1, 240));
        $ikp->setWrapSelectedButton('$sdlfk$');
        $ikp->paginate();
    }
}
