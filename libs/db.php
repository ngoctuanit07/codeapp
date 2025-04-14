<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

include_once(__DIR__ . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class DB
{
    private $ketnoi;

    // Kết nối cơ sở dữ liệu
    public function connect()
    {
        if (!$this->ketnoi) {
            $this->ketnoi = mysqli_connect(
                $_ENV['DB_HOST'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
                $_ENV['DB_DATABASE']
            ) or die('Máy chủ đang quá tải, vui lòng thử lại sau');
            mysqli_set_charset($this->ketnoi, 'utf8');
        }
    }

    // Ngắt kết nối cơ sở dữ liệu
    public function dis_connect()
    {
        if ($this->ketnoi) {
            mysqli_close($this->ketnoi);
        }
    }

    // Lấy giá trị từ bảng settings
    public function site($data)
    {
        $this->connect();
        $stmt = $this->ketnoi->prepare("SELECT `value` FROM `settings` WHERE `name` = ?");
        $stmt->bind_param('s', $data);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['value'] ?? null;
    }

    // Thực thi câu truy vấn SQL
    public function query($sql)
    {
        $this->connect();
        return $this->ketnoi->query($sql);
    }

    // Tăng giá trị cột
    public function cong($table, $data, $sotien, $where)
    {
        $this->connect();
        $sql = "UPDATE `$table` SET `$data` = `$data` + ? WHERE $where";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->bind_param('d', $sotien);
        $stmt->execute();
        $stmt->close();
    }

    // Giảm giá trị cột
    public function tru($table, $data, $sotien, $where)
    {
        $this->connect();
        $sql = "UPDATE `$table` SET `$data` = `$data` - ? WHERE $where";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->bind_param('d', $sotien);
        $stmt->execute();
        $stmt->close();
    }

    // Thêm dữ liệu vào bảng
    public function insert($table, $data)
    {
        $this->connect();
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $values = array_values($data);

        $sql = "INSERT INTO `$table` ($fields) VALUES ($placeholders)";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        $stmt->execute();
        $stmt->close();
    }

    // Cập nhật dữ liệu trong bảng
    public function update($table, $data, $where)
    {
        $this->connect();
        $set = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $values = array_values($data);

        $sql = "UPDATE `$table` SET $set WHERE $where";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        $stmt->execute();
        $stmt->close();
    }

    // Cập nhật dữ liệu với giới hạn
    public function update_value($table, $data, $where, $limit)
    {
        $this->connect();
        $set = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $values = array_values($data);

        $sql = "UPDATE `$table` SET $set WHERE $where LIMIT ?";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($values)) . 'i', ...$values, $limit);
        $stmt->execute();
        $stmt->close();
    }

    // Xóa dữ liệu trong bảng
    public function remove($table, $where)
    {
        $this->connect();
        $sql = "DELETE FROM `$table` WHERE $where";
        $stmt = $this->ketnoi->prepare($sql);
        $stmt->execute();
        $stmt->close();
    }

    // Lấy danh sách dữ liệu
    public function get_list($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            die('Câu truy vấn bị sai');
        }
        $return = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $return[] = $row;
        }
        mysqli_free_result($result);
        return $return;
    }

    // Lấy một dòng dữ liệu
    public function get_row($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            die('Câu truy vấn bị sai');
        }
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $row ?: false;
    }

    // Đếm số dòng dữ liệu
    public function num_rows($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            die('Câu truy vấn bị sai');
        }
        $row = mysqli_num_rows($result);
        mysqli_free_result($result);
        return $row ?: false;
    }
}

