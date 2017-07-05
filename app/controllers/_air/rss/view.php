<?php
/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 */
$rss = new AirRssNotifier();
echo $rss->renderItemView(r('id'));