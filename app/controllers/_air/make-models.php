<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class AirMakeModelsController
 */
class AirMakeModelsController
{
    private $classPrefix;

    function __construct()
    {
        echo "<pre>\n";
        $dbConfigName = r('db');

        $this->classPrefix = str_replace(' ', '', ucwords(str_replace('_', ' ', $dbConfigName)));
        $prefix = strtolower($this->classPrefix);

        $tableDefinitionClasses = [];
        $cfg = cfg()->db->{$dbConfigName};
        $tables = db($dbConfigName)->query("SHOW TABLES FROM `{$cfg->name}`")->fetchAllArray();
        foreach ($tables as $tc) {
            $tableName = $tc[0];
            echo "Working on {$tableName}\n";
            $tableInfo = db($dbConfigName)->query("SHOW FULL COLUMNS FROM {$tableName}")->fetchAllAssoc();
            $tableClassName = $this->getClassByTable($tableName);
            $pk = [];
            foreach ($tableInfo as $k => $tableFields) {
                $tableInfo[$k]['PhpType'] = $this->getPhpType($tableInfo[$k]["Type"]);
                if ($tableFields['Key'] == 'PRI') {
                    $pk[] = sprintf("'%s'", $tableFields['Field']);
                }

                if ($tableInfo[$k]['Default'] !== null) {
                    $tableInfo[$k]['Default'] = "'" . $tableFields['Default'] . "'";
                }
            }

            $tableDefinitionClasses[] = view("libs/air/views/db/table-class.twig", [
                "tableName" => $tableName,
                "tableClassName" => $tableClassName,
                "pk" => $pk,
                "dbConfigName" => $dbConfigName,
                "tableInfo" => $tableInfo,
                "methods" => []
            ]);

            $className = $this->getClassByTable($tableName);
            $entityClass = sprintf("%s/app/models/%s/%s.php", ROOT, $prefix, $className);

            if (!file_exists($entityClass)) {
                $entityClassContent = sprintf("<?php \n\n%s", view("libs/air/views/db/model-class.twig", [
                    "tableName" => $tableName,
                    "dbConfigName" => $dbConfigName,
                    "tableClassName" => $tableClassName
                ]));

                $dir = dirname($entityClass);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($entityClass, $entityClassContent);
            }
        }

        if (count($tableDefinitionClasses)) {
            $data = sprintf("<?php \n\n%s", join("\n\n", $tableDefinitionClasses));
            $filename = sprintf("%s/app/models/%s/%sTableDefinitions.php", ROOT, $prefix, ucwords($dbConfigName));
            file_put_contents($filename, $data);
        }
    }

    private function getClassByTable($table)
    {
        $parts = explode('_', $table);
        foreach ($parts as $key => $value) {
            $parts[$key] = ucfirst($value);
        }
        return $this->classPrefix . join('', $parts);
    }

    private function getPhpType($mySqlType)
    {
        $types = [
            '/int\(\d+\)/' => "integer",
            '/varchar\(\d+\)/' => "string",
        ];

        foreach ($types as $re => $phpType) {
            if (preg_match($re, $mySqlType)) {
                return $phpType;
            }
        }

        return "string";
    }
}

new AirMakeModelsController();