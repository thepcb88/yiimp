<?php

$a = controller()
    ->memcache
    ->memcache
    ->get('url-map');

function printStats($stat)
{
    echo "<br><div class='ui-widget'>";
    echo "<div style='padding:5px' class='ui-widget-header ui-corner-tl ui-corner-tr'>Memcached</div>";
    echo "<div style='padding:5px' class='ui-widget-content ui-corner-bl ui-corner-br'>";

    echo "<br><table>";
    echo "<tr><td>Memcache Server version</td><td> " . $stat["version"] . "</td></tr>";
    echo "<tr><td>Process id of this server process</td><td>" . $stat["pid"] . "</td></tr>";
    echo "<tr><td>Server uptime </td><td>" . $stat["uptime"] . " seconds</td></tr>";
    echo "<tr><td>Accumulated user time for this process</td><td>" . round($stat["rusage_user"], 1) . " seconds</td></tr>";
    echo "<tr><td>Accumulated system time for this process</td><td>" . round($stat["rusage_system"], 1) . " seconds</td></tr>";
    echo "<tr><td>Total number of items stored by this server start</td><td>" . $stat["total_items"] . "</td></tr>";
    echo "<tr><td>Number of open connections </td><td>" . $stat["curr_connections"] . "</td></tr>";
    echo "<tr><td>Total number of connections opened since server start</td><td>" . $stat["total_connections"] . "</td></tr>";
    echo "<tr><td>Number of connection structures allocated by the server</td><td>" . $stat["connection_structures"] . "</td></tr>";
    echo "<tr><td>Cumulative number of retrieval requests</td><td>" . $stat["cmd_get"] . "</td></tr>";
    echo "<tr><td> Cumulative number of storage requests</td><td>" . $stat["cmd_set"] . "</td></tr>";

    $percCacheHit = ((float)$stat["get_hits"] / (float)$stat["cmd_get"] * 100);
    $percCacheHit = round($percCacheHit, 3);
    $percCacheMiss = 100 - $percCacheHit;

    echo "<tr><td>Number of keys that have been requested and found</td><td>" . $stat["get_hits"] . " ($percCacheHit%)</td></tr>";
    echo "<tr><td>Number of items that have been requested and not found</td><td>" . $stat["get_misses"] . " ($percCacheMiss%)</td></tr>";

    $MBRead = (float)$stat["bytes_read"] / (1024 * 1024);

    echo "<tr><td>Total number of bytes read by this server from network</td><td>" . $MBRead . " MB</td></tr>";
    $MBWrite = (float)$stat["bytes_written"] / (1024 * 1024);
    echo "<tr><td>Total number of bytes sent by this server to network</td><td>" . $MBWrite . " MB</td></tr>";
    $MBSize = (float)$stat["limit_maxbytes"] / (1024 * 1024);
    echo "<tr><td>Size allowed to use for storage</td><td>" . $MBSize . " MB</td></tr>";
    echo "<tr><td>Items removed from cache to free memory for new items</td><td>" . $stat["evictions"] . "</td></tr>";
    echo "</table>";
}

printStats($this
    ->memcache
    ->memcache
    ->getStats());

$res = array();

function cmp($a, $b)
{
    return $a[2] < $b[2];
}

if (!empty($a)) foreach ($a as $url => $n)
{
    $d = $this
        ->memcache
        ->get("$url-time");
    $avg = $d / $n;

    $res[] = array(
        $url,
        $n,
        $d,
        $avg
    );
}

usort($res, 'cmp');

echo '<div style="margin-top: 8px; margin-bottom: 24px; margin-right: 16px;">';
echo '<table class="dataGrid">';
echo "<thead>";
echo "<tr>";
echo "<th>Url</th>";
echo "<th align=right>Count</th>";
echo "<th align=right>Time</th>";
echo "<th align=right>Average</th>";
echo "</tr>";
echo "</thead><tbody>";

if (!empty($res)) foreach ($res as $item)
{
    //	debuglog("$i => $n");
    $url = $item[0];
    $n = $item[1];
    $d = round($item[2], 3);
    $avg = round($item[3], 3);

    echo "<tr class='ssrow'>";
    echo "<td><a href='/$url'>$url</a></td>";
    echo "<td align=right>$n</td>";
    echo "<td align=right>$d</td>";
    echo "<td align=right>$avg</td>";
    echo "</tr>";
}

echo "</tbody></table></div></div></div><br>";

echo "<button id=button class='ui-state-default ui-corner-all' style='padding: 5px 15px 5px 15px'><a href='/site/memcached'>Refresh</a></button>";
