<?php
namespace nl\mondriaan\ao\trainingsfactory\models;

use ao\php\framework\models\AbstractModel;
class InstructeurModel extends AbstractModel
{
    public function __construct($control, $action) {
    parent::__construct($control, $action);
    }

    public function isGerechtigd() {
    //controleer of er ingelogd is. Ja, kijk of de gebuiker deze controller mag gebruiken
    if(isset($_SESSION['gebruiker'])&&!empty($_SESSION['gebruiker']))
    {
        $gebruiker=$_SESSION['gebruiker'];
        if ($gebruiker->getRole() == "instructeur")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    return false;
    }

    public function uitloggen() {
    $_SESSION = array();
    session_destroy();
    }
    
    public function getLessen() {
        $sql = "SELECT * FROM `lessons`";
        $stmnt = $this->dbh->prepare($sql);
        $stmnt->execute();
        $lessen = $stmnt->fetchAll(\PDO::FETCH_CLASS,__NAMESPACE__.'\db\Les');    
        return $lessen;      
    }
    
    public function verwijderLes() {
        $les_id  = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        $sql = "DELETE FROM `lessons` WHERE `id` = :id";
        $stmnt = $this->dbh->prepare($sql);
        $stmnt->bindParam(':id',$les_id);
        $stmnt->execute();
        $aantalGewijzigd = $stmnt->rowCount();
        if($aantalGewijzigd === 1)
        {
            return REQUEST_SUCCESS;
        }
        return REQUEST_NOTHING_CHANGED;
    }
    
    public function wijzigLes() {
        $tijd = filter_input(INPUT_POST,'tijd');
        $datum = filter_input(INPUT_POST,'datum');
        $locatie = filter_input(INPUT_POST,'locatie');
        $maxpers = filter_input(INPUT_POST,'maxpers');
        $les_id  = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        

        if($datum===null || $tijd===null || $locatie===null || $maxpers===null) {
            return REQUEST_FAILURE_DATA_INCOMPLETE;
        }
        if(empty($datum)||empty($tijd)||empty($locatie)||empty($maxpers)) {
            return REQUEST_FAILURE_DATA_INCOMPLETE;
        }
        
        $sql = "UPDATE `lessons` SET time=:tijd,date=:datum,location=:locatie,max_persons=:maxpers  WHERE id = $les_id";
        $stmnt = $this->dbh->prepare($sql);
        $stmnt->bindParam(':tijd', $tijd);
        $stmnt->bindParam(':datum', $datum);
        $stmnt->bindParam(':locatie', $locatie);
        $stmnt->bindParam(':maxpers', $maxpers);
        $stmnt->execute();
        $aantalGewijzigd = $stmnt->rowCount();
        if($aantalGewijzigd === 1)
        {
            return REQUEST_SUCCESS;
        }
        return REQUEST_NOTHING_CHANGED;
    }
    public function voegLesToe() {
        $tijd = filter_input(INPUT_POST,'tijd');
        $datum = filter_input(INPUT_POST,'datum');
        $locatie = filter_input(INPUT_POST,'locatie');
        $maxpers = filter_input(INPUT_POST,'maxpers');

        if($datum===null || $tijd===null || $locatie===null || $maxpers===null) {
            return REQUEST_FAILURE_DATA_INCOMPLETE;
        }
        if(empty($datum)||empty($tijd)||empty($locatie)||empty($maxpers)) {
            return REQUEST_FAILURE_DATA_INCOMPLETE;
        }
        
        $id = $this->getGebruiker()->getId();
        $sql = "INSERT INTO `lessons` (time,date,location,max_persons) VALUES (:tijd,:datum,:locatie,:maxpers)";
        $stmnt = $this->dbh->prepare($sql);
        $stmnt->bindParam(':tijd', $tijd);
        $stmnt->bindParam(':datum', $datum);
        $stmnt->bindParam(':locatie', $locatie);
        $stmnt->bindParam(':maxpers', $maxpers);
        $stmnt->execute();
        $aantalGewijzigd = $stmnt->rowCount();
        if($aantalGewijzigd === 1)
        {
            return REQUEST_SUCCESS;
        }
        return REQUEST_NOTHING_CHANGED;
    }
    public function getLes() {
        $les_id  = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
        $sql = "SELECT * FROM `lessons` WHERE `id` = :id";
        $stmnt = $this->dbh->prepare($sql);
        $stmnt->bindParam(':id',$les_id);
        $stmnt->execute();
        $les = $stmnt->fetchAll(\PDO::FETCH_CLASS,__NAMESPACE__.'\db\Les');    
        return $les[0];  
    }
}