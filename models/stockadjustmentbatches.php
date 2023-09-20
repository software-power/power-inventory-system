<?php


class StockAdjustmentBatches extends model
{

    public const ACTION_ADD = 'add';
    public const ACTION_REDUCE = 'reduce';
    var $table = 'stock_adjustment_batches';
}