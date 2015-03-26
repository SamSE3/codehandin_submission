<?php



class file_storage {

    private $contextid, $component, $filearea;

    /**
     * 
     * @param int $contextid This parameter and the next two identify the file area to copy files from.
     * @param string $component
     * @param string $filearea helps indentify the file area.
     */
    public function __construct($contextid) {
        $this->contextid = $contextid;
        $this->component = 'assignsubmission_codehandin';
        $this->filearea = 'testing_files';
    }

    private function getkey($path) {
        return str_replace('/', '', $path);
    }

    /**
     * Fetch file using local file full pathname hash
     *
     * @param string $pathnamehash path name hash
     * @return stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file_by_hash($pathnamehash) {
        global $DB;

        $sql = "SELECT " . self::instance_sql_fields('f', 'r') . "
                  FROM {files} f
             LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.pathnamehash = ?";
        if ($filerecord = $DB->get_record_sql($sql, array($pathnamehash))) {
            return $this->get_file_instance($filerecord);
        } else {
            return false;
        }
    }

    /**
     * Fetch locally stored file.
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return stored_file|bool stored_file instance if exists, false if not
     */
    public function get_file($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        $filepath = clean_param($filepath, PARAM_PATH);
        $filename = clean_param($filename, PARAM_FILE);

        if ($filename === '') {
            $filename = '.';
        }

        $pathnamehash = $this->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename);
        return $this->get_file_by_hash($pathnamehash);
    }

}
