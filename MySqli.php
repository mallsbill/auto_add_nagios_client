Constants : MYSQLI_ASSOC,MYSQLI_NUM,MYSQLI_BOTH

    class my_mysqli
        $conn;
        $host_info
        $protocol_version
        $thread_id
        $error
        $errno
        $affected_rows
        $server_info
        $client_info
        $info
        $insert_id;
        function query() 
        function real_escape_string() 
        function close() 
        function set_charset()
        function stat()

    class my_mysqli_query
        $query
        $num_rows
        $field_count
        function fetch_all()
        function fetch_assoc() 
        function fetch_array()
        function fetch_row()
        function fetch_field()
        function free()
