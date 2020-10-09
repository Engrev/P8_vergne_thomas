<?php

namespace App\Tests;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskTest
 * @package App\Tests
 */
class TaskTest extends TestCase
{
    public function testCreateTask()
    {
        $task = new Task();
        $title = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 10);

        $task->setTitle($title)
            ->setContent('Lorem ipsum dolor sit amet.')
        ;

        $this->assertInstanceOf(Task::class, $task);
    }
}