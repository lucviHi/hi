<?php

namespace App\Services;

use App\Models\LivePerformanceSnap;

class LivePerformanceSnapService
{
    public static function snapshot(array $data): LivePerformanceSnap
    {
        $roomId = $data['room_id'];
        $date = $data['date'];
        $type = $data['type'] ?? 'daily';
        $hour = $type === 'hourly' ? ($data['hour'] ?? null) : null;
       
        $snap = LivePerformanceSnap::firstOrNew([
            'room_id' => $roomId,
            'date' => $date,
            'hour' => $hour,
            'type' => $type,
       
        ]);

        $fields = [
            'gmv', 'ads_total_cost', 'views', 'live_impressions', 'items_sold',
            'product_clicks', 'comments', 'shares', 'entry_rate', 'ctr', 'ctor',
            'main_host_id', 'support_host_id'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $snap->$field = $data[$field];
            }
        }

        $snap->save();
        return $snap;
    }
}
