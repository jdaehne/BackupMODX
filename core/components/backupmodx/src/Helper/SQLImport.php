<?php
/**
 * SQLImport
 *
 * This script imports SQL commands "as is". Double check the SQL commands
 * before using it and BACKUP YOUR DATABASE.
 *
 * Based on https://gist.github.com/b4oshany/0698d9f32589b77abdcb
 *
 * @package backupmodx
 * @subpackage classfile
 */

namespace BackupMODX\Helper;

use Exception;
use modX;

class SQLImport
{
    /**
     * Loads an SQL stream into the database one command at a time.
     *
     * @param modX $modx A reference to the modX instance.
     * @param string $sqlFile The filename of the file containing the mysql-dump data.
     * @param bool $logQuery Return a log of all executed queries.
     * @return bool|string|array Returns true or ["success" => true, "log" => $log], if the SQL was imported successfully.
     * @throws Exception
     */
    public static function importSQL(modX $modx, $sqlFile, $logQuery = false)
    {
        # read file into array
        $file = file($sqlFile);

        # import file line by line
        # and filter (remove) those lines, beginning with an sql comment token
        $file = array_filter($file, function ($line) {
            return strpos(ltrim($line), '--') !== 0;
        });
        # and filter (remove) those lines, beginning with an sql notes token
        $file = array_filter($file, function ($line) {
            return strpos(ltrim($line), '/*') !== 0;
        });

        $sql = '';
        $log = '';
        $del_num = false;
        foreach ($file as $line) {
            $query = trim($line);
            $delimiter = is_int(strpos($query, 'DELIMITER'));
            if ($delimiter || $del_num) {
                if ($delimiter && !$del_num) {
                    $sql = $query . '; ';
                    if ($logQuery) {
                        $log .= "OK\n---\n";
                    }
                    $del_num = true;
                } else {
                    if ($delimiter && $del_num) {
                        $sql .= $query . ' ';
                        $del_num = false;
                        if ($logQuery) {
                            $log .= "{$sql}\ndo---do\n";
                        }
                        $stmt = $modx->prepare($sql);
                        if (!$stmt->execute()) {
                            throw new Exception(implode(' ', $stmt->errorInfo()) . "\n" . 'SQL Query: ' . $query);
                        }
                        $sql = '';
                    } else {
                        $sql .= $query . '; ';
                    }
                }
            } else {
                $delimiter = is_int(strpos($query, ';'));
                if ($delimiter) {
                    $stmt = $modx->prepare("{$sql} {$query}");
                    if (!$stmt->execute()) {
                        throw new Exception(implode(' ', $stmt->errorInfo()) . "\n" . 'SQL Query: ' . $query);
                    }
                    if ($logQuery) {
                        $log .= "{$sql} {$query}\n---\n";
                    }
                    $sql = '';
                } else {
                    $sql .= ' ' . $query;
                }
            }
        }

        return ($logQuery) ? $log : true;
    }
}


