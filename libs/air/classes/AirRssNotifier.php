<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class AirRssNotifier
 */
class AirRssNotifier
{
    protected $dataDir;

    function __construct()
    {
        $this->dataDir = storage_path('rss');
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }

    function renderItemView($id)
    {
        $filename = $this->dataDir . "/" . $id . ".json";

        if (!file_exists($filename)) {
            return "Not found";
        }

        $json = file_get_contents($filename);
        $message = json_decode($json);

        header('Content-Type: text/plain');
        return join("\n\n", [
            $message->subject,
            $message->body,
            $message->ts
        ]);
    }


    function renderListView($limit)
    {
        $items = [];

        foreach ($this->getList() as $filename) {

            if (!$json = @file_get_contents($filename)) {
                continue;
            } elseif (!$message = @json_decode($json)) {
                continue;
            }

            $message->id = pathinfo($filename, PATHINFO_FILENAME);
            $message->body = $this->filterBody($message->body);
            $message->body = nl2br($message->body);
            $message->ts = date("r", strtotime($message->ts));
            $message->link = url('_air/rss/view', [
                'id' => $message->id
            ]);

            $items[] = $message;

            if (count($items) >= $limit) {
                break;
            }
        }

        header('Content-Type: application/rss+xml; charset=UTF-8');
        return view('libs/air/views/rss/list.twig', [
            'pubDate' => date("r"),
            'viewMsgUrl' => url('_air/rss/view'),
            'items' => $items
        ]);
    }

    function addItem($subject, $body)
    {
        $filename = $this->dataDir . "/" . date('YmdHis') . '-' . uniqid() . '.json';

        $data = [
            'subject' => $subject,
            'body' => $body,
            'ts' => date('Y-m-d H:i:s')
        ];

        @file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        @chmod($filename, 0766);
        $this->gc();
    }

    protected function filterBody($body)
    {
        return preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[\x00-\x7F][\x80-\xBF]+' .
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '?', $body);
    }

    protected function getList()
    {
        $list = [];

        foreach (glob("{$this->dataDir}/*.json") as $filename) {
            $list[$filename] = filectime($filename);
        }

        arsort($list, SORT_NUMERIC);
        return array_keys($list);
    }

    /**
     * Remove old files from dataDir
     */
    protected function gc()
    {
        if (!mt_rand(0, 100) > 80) {
            return;
        }

        $expireTime = time() - (1 * 24 * 3600);
        foreach (glob("{$this->dataDir}/*.json") as $filename) {
            if (filectime($filename) < $expireTime) {
                @unlink($filename);
            }
        }
    }
}