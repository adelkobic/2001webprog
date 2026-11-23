<?php

class Validator {
    public static function required($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new AppException(ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }
    }

    public static function email($email) {
        if (!filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL)) {
            throw new AppException('Email is not valid.');
        }
    }

    public static function password($password) {
        if (strlen((string) $password) < 6) {
            throw new AppException('Password must be at least 6 characters.');
        }
    }

    public static function phone($phone) {
        $phone = trim((string) $phone);
        if (strlen($phone) < 6 || strlen($phone) > 30 || !preg_match('/\d/', $phone)) {
            throw new AppException('Phone is not valid.');
        }
    }

    public static function isbn($isbn) {
        $clean = preg_replace('/[^0-9Xx]/', '', (string) $isbn);
        if (strlen($clean) !== 10 && strlen($clean) !== 13) {
            throw new AppException('ISBN must be 10 or 13 characters.');
        }
        return strtoupper($clean);
    }

    public static function year($year) {
        if (!is_numeric($year)) {
            throw new AppException('Publication year is not valid.');
        }
        $year = (int) $year;
        $max = (int) date('Y') + 1;
        if ($year < 1000 || $year > $max) {
            throw new AppException('Publication year is not valid.');
        }
        return $year;
    }

    public static function copies($copies) {
        if (!is_numeric($copies) || (int) $copies != $copies || (int) $copies < 1) {
            throw new AppException('Total copies must be at least 1.');
        }
        return (int) $copies;
    }

    public static function rating($rating) {
        if (!is_numeric($rating) || (int) $rating < 1 || (int) $rating > 5) {
            throw new AppException('Rating must be from 1 to 5.');
        }
        return (int) $rating;
    }

    public static function dueDate($date) {
        $date = self::date($date, 'Due date');
        if ($date < date('Y-m-d')) {
            throw new AppException('Due date cannot be in the past.');
        }
        return $date;
    }

    public static function date($date, $label) {
        $parsed = DateTime::createFromFormat('Y-m-d', (string) $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            throw new AppException($label . ' is not valid.');
        }
        return $date;
    }

    public static function role($role) {
        if (!in_array($role, ['admin', 'staff', 'patron'], true)) {
            throw new AppException('Role is not valid.');
        }
        return $role;
    }

    public static function text($value, $label, $max) {
        $value = trim((string) $value);
        if ($value === '') {
            throw new AppException($label . ' is required.');
        }
        if (strlen($value) > $max) {
            throw new AppException($label . ' is too long.');
        }
        return $value;
    }
}
