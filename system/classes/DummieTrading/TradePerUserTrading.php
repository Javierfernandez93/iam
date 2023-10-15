<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\OrderTypeParser;

class TradePerUserTrading extends Orm {
  protected $tblName  = 'trade_per_user_trading';

  const PENDING = 0;
  const PLACED = 1;
  const IN_PROGRESS_TO_PLACE = -3;
  const DELETED_FOR_PLACED = -4;
  const MINUTES_TO_PLACE_ORDER = 5;

  public function __construct() {
    parent::__construct();
  }

  public static function closeOrderByTicketId(string $ticket = null) : bool
  {
    if(isset($ticket))
    {
      $TradePerUserTrading = new self;

      if($TradePerUserTrading->loadWhere("ticket = ?",$ticket))
      {
        $TradePerUserTrading->close = 1;

        return $TradePerUserTrading->save();
      }
    }

    return false;
  }

  public static function appendToInprogressOrder(array $data = null) : bool
  {
    if(isset($data['user_trading_account_id']))
    {
      $TradePerUserTrading = new self;

      if($order = $TradePerUserTrading->getLastOrderInProgress($data['user_trading_account_id']))
      {
        $TradePerUserTrading->loadWhere("trade_per_user_trading_id = ?",$order['trade_per_user_trading_id']);
      } else {
        $TradePerUserTrading->user_trading_account_id = $data['user_trading_account_id'];
        $TradePerUserTrading->create_date = time();
        $TradePerUserTrading->status = self::IN_PROGRESS_TO_PLACE;
      }
      
      if(isset($data['symbol']))
      {
        $TradePerUserTrading->symbol = $data['symbol'];
      } else if(isset($data['buy'])) {
        $TradePerUserTrading->buy = $data['buy'];
      } else if(isset($data['lotage'])) {
        $TradePerUserTrading->lotage = $data['lotage'];
      } else if(isset($data['price_entrace'])) {
        $TradePerUserTrading->price_entrace = $data['price_entrace'];
      } else if(isset($data['take_profit'])) {
        $TradePerUserTrading->take_profit = $data['take_profit'];
      } else if(isset($data['stop_loss'])) {
        $TradePerUserTrading->stop_loss = $data['stop_loss'];
      }
      
      return $TradePerUserTrading->save();
    }

    return false;
  }

  
  public static function pushOrders(int $user_trading_account_id = null,array $data = null) : array|bool
  {
    if(isset($data))
    {
      $TradePerUserTrading = new self;

      // $data = array_filter($data,function($order) use($TradePerUserTrading) {
      //   return !$TradePerUserTrading->existTicket($order['id']);
      // });
      
      if(sizeof($data) > 0)
      {
        return array_map(function($order) use($user_trading_account_id) {
          $order = self::sanitizeOrder([
            ...$order,
            ...['user_trading_account_id' => $user_trading_account_id]
          ]);

          $order['trade_per_user_trading_id'] = self::pushOrder($order);

          return $order;
        },$data);
      }
    }

    return false;
  }

  public static function sanitizePosition(string $type = null) : int 
  {
    if($type == 'POSITION_TYPE_BUY') {
      return 1;
    } else if($type == 'POSITION_TYPE_BUY') {
      return 0;
    }

    return 0;
  }

  public static function sanitizeOrder(array $data = null) : array {
    return [
      'profit' => 0,
      'ticket' => $data['id'],
      'lotage' => $data['volume'],
      'price' => $data['openPrice'],
      'profit' => $data['profit'],
      'buy' => self::sanitizePosition($data['type']),
      'symbol' => $data['symbol'],
      'user_trading_account_id' => $data['user_trading_account_id'],
      'status' => self::PLACED,
    ];
  }

  public static function pushOrder(array $data = null) : bool|int
  {
    if(isset($data))
    {
      $TradePerUserTrading = new self;
      $TradePerUserTrading->loadWhere("ticket = ?",$data['ticket']);

      $TradePerUserTrading->ticket = $data['ticket'] ?? 0;
      $TradePerUserTrading->profit = $data['profit'] ?? $TradePerUserTrading->profit;
      $TradePerUserTrading->lotage = $data['lotage'] ?? 0;
      $TradePerUserTrading->price = $data['price'] ?? 0;
      $TradePerUserTrading->buy = $data['buy'] ?? 0;
      $TradePerUserTrading->symbol = $data['symbol'] ?? '';
      $TradePerUserTrading->user_trading_account_id = $data['user_trading_account_id'] ?? 42;
      $TradePerUserTrading->status = $data['status'] ?? self::PENDING;
      $TradePerUserTrading->create_date = time();

      return $TradePerUserTrading->save() ? $TradePerUserTrading->getId() : false;
    }

    return false;
  }

  public static function add(array $data = null) : int|bool
  {
    if(isset($data))
    {
      $TradePerUserTrading = new self;
      $TradePerUserTrading->loadWhere('ticket = ?',$data['ticket']);
    
      if(!$TradePerUserTrading->getId())
      {
        $TradePerUserTrading->create_date = $data['time'] ?? time(); // @todo
        $TradePerUserTrading->user_trading_account_id = $data['user_trading_account_id'];
        $TradePerUserTrading->ticket = $data['ticket'] ?? 0;
        $TradePerUserTrading->lotage = $data['lotage'] ?? 0;
        $TradePerUserTrading->symbol = $data['symbol'] ?? '';
      }

      $TradePerUserTrading->price_entrace = $data['price_entrace'] ?? 0;
      $TradePerUserTrading->take_profit = $data['take_profit'] ?? 0;
      $TradePerUserTrading->stop_loss = $data['stop_loss'] ?? 0;
      $TradePerUserTrading->profit = $data['profit'] ?? 0;
      $TradePerUserTrading->price = $data['price'] ?? 0;
      $TradePerUserTrading->buy = $data['buy'] ?? 0;
      
      return $TradePerUserTrading->save() ? $TradePerUserTrading->getId() : false;
    }

    return false;
  }

  public function getAllFromUser(array $data = null,string $filter = null) 
  {
    $filter = "";

    if(isset($data['start_date']) && !empty($data['start_date']))
    {
        $filter = " AND trade_per_user_trading.create_date BETWEEN '".strtotime($data['start_date'])."' AND '".strtotime($data['end_date'])."'";
    }

    if(isset($data))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.ticket,
                {$this->tblName}.profit,
                {$this->tblName}.price,
                {$this->tblName}.lotage,
                {$this->tblName}.buy,
                {$this->tblName}.symbol,
                {$this->tblName}.close,
                {$this->tblName}.create_date,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$data['user_trading_account_id']}'
                {$filter}
              AND 
                {$this->tblName}.status = '1'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              LIMIT 15
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getLastTrade(int $user_trading_account_id = null) : float
  {
    if(isset($user_trading_account_id))
    {
      $sql = "SELECT
                {$this->tblName}.profit
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status = '1'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getLastTrades(int $user_trading_account_id = null) : array|bool
  {
    if(isset($user_trading_account_id))
    {
      $sql = "SELECT
                {$this->tblName}.ticket,
                {$this->tblName}.profit,
                {$this->tblName}.create_date
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status = '1'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              LIMIT 5
              ";
              
      if($trades = $this->connection()->rows($sql))
      {
        return $trades;
      }
    }

    return false;
  }
  
  public function existTicket(string $ticket = null) : bool
  {
    if(isset($ticket))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.ticket = '{$ticket}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      if($this->connection()->field($sql))
      {
        return true;
      }
    }

    return false;
  }

  public static function setOrderAsPlaced(array $data = null) : bool
  {
    $TradePerUserTrading = new self;
    
    if($TradePerUserTrading->loadWhere('trade_per_user_trading_id = ? AND status = ?',[$data['trade_per_user_trading_id'],self::PENDING]))
    {
      $TradePerUserTrading->ticket = $data['ticket'];
      $TradePerUserTrading->status = self::PLACED;
      
      return $TradePerUserTrading->save();
    }

    return false;
  }

  public function getPendingOrder(int $user_trading_account_id = null,string $filter = null) 
  {
    if(isset($user_trading_account_id))
    {
      $limit_time = strtotime("-".self::MINUTES_TO_PLACE_ORDER." minutes");

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.ticket,
                {$this->tblName}.profit,
                {$this->tblName}.price,
                {$this->tblName}.lotage,
                {$this->tblName}.buy,
                {$this->tblName}.symbol,
                {$this->tblName}.create_date,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
                {$filter}
              AND 
                {$this->tblName}.status = '".self::PENDING."'
              AND 
                {$this->tblName}.create_date >= '{$limit_time}'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getOrderInfo(int $trade_per_user_trading_id = null) 
  {
    if(isset($trade_per_user_trading_id))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.ticket,
                {$this->tblName}.profit,
                {$this->tblName}.price,
                {$this->tblName}.lotage,
                {$this->tblName}.buy,
                {$this->tblName}.symbol,
                {$this->tblName}.create_date,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.trade_per_user_trading_id = '{$trade_per_user_trading_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public static function _getLastOrderInProgress(int $user_trading_account_id = null) : array|bool
  {
    $TradePerUserTrading = new self;
    
    if($order = $TradePerUserTrading->getLastOrderInProgress($user_trading_account_id))
    {
      if($TradePerUserTrading->loadWhere("trade_per_user_trading_id = ?",$order['trade_per_user_trading_id']))
      {
        $TradePerUserTrading->status = self::DELETED_FOR_PLACED;
        $TradePerUserTrading->save();

        return [
          'symbol' => $order['symbol'],
          'lotage' => $order['lotage'],
          'takeProfit' => $order['take_profit'],
          'stopLoss' => $order['stop_loss'],
          'priceEntrace' => $order['price_entrace'],
          'buy' => $order['buy'],
          'type' => OrderTypeParser::getOrderTextByInt($order['buy']),
        ];
      }
    }

    return false;
  }

  public function getLastOrderInProgress(int $user_trading_account_id = null) : array|bool
  {
    if(isset($user_trading_account_id))
    {
      $limit_time = strtotime("-".self::MINUTES_TO_PLACE_ORDER." minutes");

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.symbol,
                {$this->tblName}.take_profit,
                {$this->tblName}.stop_loss,
                {$this->tblName}.price_entrace,
                {$this->tblName}.price,
                {$this->tblName}.lotage,
                {$this->tblName}.buy
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status = '".self::IN_PROGRESS_TO_PLACE."'
              AND 
                {$this->tblName}.create_date >= '{$limit_time}'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
}