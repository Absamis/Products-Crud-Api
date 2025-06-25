<?php

namespace App\Jobs;

use App\Models\Product;
use App\Notifications\ExpiryNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendExpiryNotification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        echo "Running expiry jobs";
        $now = date("Y-m-d");
        $minDay = config("services.utils.min_days");
        $products = Product::query()->withoutGlobalScope("for-user")->where("status", 1)->orWhere("status", 0)->get();
        foreach($products as $product){
            $nDay = (strtotime($product->expiry_date) - time())/60/60/24;
            $seen = false;
            if($nDay <= 0 ){
                $seen = true;
                $product->status = -1;
            }else if($nDay <= $minDay ){
                $seen = true;
                $product->status = 0;
            }
            if($seen){
                $product->save();
                $nd = (int)$nDay;
                Notification::send($product->user, new ExpiryNotification($product, $nd));
            }
        }
    }
}
