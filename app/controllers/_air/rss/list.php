<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 */
$rss = new AirRssNotifier();
echo $rss->renderListView(r('limit', 50));