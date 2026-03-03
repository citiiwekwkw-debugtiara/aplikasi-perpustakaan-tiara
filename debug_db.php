<?php
require_once 'config/database.php';

$res = mysqli_query($koneksi, "SHOW TABLES");
if ($res) {
    echo "Tables in database:\n";
    while ($row = mysqli_fetch_row($res)) {
        echo "- " . $row[0] . "\n";
        
        $desc = mysqli_query($koneksi, "DESCRIBE " . $row[0]);
        if ($desc) {
            while ($d = mysqli_fetch_assoc($desc)) {
                echo "  * " . $d['Field'] . " (" . $d['Type'] . ")\n";
            }
        }
    }
} else {
    echo "Error showing tables: " . mysqli_error($koneksi);
}
?>
