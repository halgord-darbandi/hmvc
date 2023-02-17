<?php

namespace App\Database;

use App\Database\Connection;

class QueryBuilder
{
    private $table;
    private $conditions;
    private $connection;

    public function __construct()
    {
        $connection = new Connection();
        $this->connection = $connection->Connection();
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function where($condition, $value)
    {
        $this->conditions[$condition] = $value;
        return $this;
    }

    public function count($column = 'id')
    {
        $where = [];
        foreach ($this->conditions as $condition => $value) {
            $where[] = $condition . '=' . "'$value'";
        }
        $where = implode(' AND ', $where);

        $sql = "SELECT count($column) FROM {$this->table} WHERE {$where}";
        $res = $this->connection->query($sql);
        $this->flushWhere();
        $count = $res->fetch_Array();
        return (int)$count["count($column)"];
    }

    public function select(array $options = [])
    {
        /* options:
        properties array|string
        order_by
        order
        return_method
        */


        if (!empty($this->conditions)) {
            $where = [];
            foreach ($this->conditions as $condition => $value) {
                $where[] = $condition . '=' . "'$value'";
            }
            $where = implode(' AND ', $where);
        } else {
            $where = null;
        }
        if (isset($options['properties'])) {
            if (!is_string($options['properties'])) {
                $properties = [];
                foreach ($options['properties'] as $property) {
                    $properties[] = $property;
                }
                $properties = implode(',', $properties);
            } else {
                $properties = $options['properties'];
            }
        } else {
            $properties = '*';
        }


        if (!is_null($where)) {
            $hasWhere = 'WHERE';
        } else {
            $hasWhere = '';
        }

        if (isset($options['order_by'])) {
            $hasOrder = "ORDER BY {$options['order_by']}";
        } else {
            $hasOrder = '';
        }

        if (isset($options['order'])) {
            $order = $options['order'];
        } else {
            $order = '';
        }

        if (isset($options['return_method'])) {
            $return_method = $options['return_method'];
        } else {
            $return_method = 'fetch_all';
        }

        $sql = "SELECT {$properties} FROM {$this->table} {$hasWhere} {$where} {$hasOrder} {$order}";
        $res = $this->connection->query($sql);
        $this->flushWhere();
        return $res->{$return_method}(1);
    }

    public function delete()
    {
        $where = [];
        foreach ($this->conditions as $condition => $value) {
            $where[] = $condition . '=' . "'$value'";
        }
        $where = implode(' AND ', $where);
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        $this->flushWhere();
        return $this->connection->query($sql);
    }

    public function update($data)
    {
        $update = [];
        $where = [];

        // for editing value
        foreach ($data as $item => $value) {
            if (str_starts_with($value, '@')) {
                $value = substr($value, '1');
                $update[] = "$item = $value";
                continue;
            }
            $update[] = "$item = '$value'";
        }

        foreach ($this->conditions as $condition => $value) {
            $where[] = $condition . '=' . "'$value'";
        }
        $where = implode(' AND ', $where);
        $update = implode(',', $update);


        $sql = "UPDATE {$this->table} SET {$update}  WHERE {$where}";
        $this->flushWhere();
        return $this->connection->query($sql);
    }

    public function create($data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $item => $value) {
            $fields[] = $item;
            $values[] = "'$value'";
        }

        $fields = implode(',', $fields);
        $values = implode(',', $values);

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";
        return $this->connection->query($sql);
    }

    private function flushWhere()
    {
        $this->conditions = [];
    }

    public function insert_id()
    {
        return $this->connection->insert_id;
    }
}