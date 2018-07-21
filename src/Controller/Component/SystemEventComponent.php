<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\Time;
use Cake\Log\Log;


class SystemEventComponent extends Component
{
    public function create($title, $starting_date)
    {
        $data = [];
        $start = new Time($starting_date);
        $end = new Time($starting_date);
        $end = $end->addDays(1);

        return [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'all_day' => 'true',
            'scope' => '2',
            'color' => 'red'
        ];
    }

    public function update($id, $starting_date)
    {
        $data = [];
        $start = new Time($starting_date);
        $end = new Time($starting_date);
        $end = $end->addDays(1);

        return [
            'id' => $id,
            'start' => $start,
            'end' => $end,
        ];
    }
}