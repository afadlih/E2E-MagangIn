<?php

namespace App\Services;

class SmartRecommendationService
{
    protected array $weights = [
        'pref'          => 0.35,
        'skill'         => 0.20,
        'lokasi'        => 0.20,
        'tipe_bekerja'  => 0.15,   
        'durasi'        => 0.10,
    ];

    /**
     * @param array $data
     * @return array sorted by ['id','score']
     */
    public function rank(array $data): array
    {
        $min = $max = [];
        foreach ($this->weights as $key => $_) {
            $values = array_column($data, $key);
            if (empty($values)) {
                $min[$key] = 0;
                $max[$key] = 1;
                continue;
            }
            $min[$key] = min($values);
            $max[$key] = max($values);
        }

        foreach ($data as &$item) {
            $sum = 0;
            foreach ($this->weights as $key => $w) {
                $range = ($max[$key] - $min[$key]) ?: 1;
                $u = ($item[$key] - $min[$key]) / $range;
                $sum += ($u * $w);
            }
            $item['score'] = round($sum, 4);
        }
        unset($item);

        usort($data, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_map(fn($i) => [
            'id'    => $i['id'],
            'score' => $i['score']
        ], $data);
    }
}
