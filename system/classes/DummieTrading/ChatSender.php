<?php

namespace DummieTrading;

use HCStudio\Orm;

class ChatSender extends Orm {
    public static $ROBOT = 0;
    public static $USER = 1;
    public static $ADMIN = 2;
}