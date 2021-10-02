<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Deliver;
use Illuminate\Console\Command;

class DeliveredOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delivered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Everyday 12:am all delivered order should be moved to delivers table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Order::where('status','deliverd')->get();
        foreach ($orders as $value) {
            Deliver::create([
                'order_id' => $value->id,
                'user_id' => $value->user_id,
                'product_id' => $value->product_id,
                'qty' => $value->qty,
                'price' => $value->price
            ]);
        }
    }
}
