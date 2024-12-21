<?php
include "includes/config.php";
$url = "personnels.php";

#page logics
//Add Personnel
if(isset($_POST['addPersonnel'])){
    if(isset($_POST['iAgree'])){
          #data collection & sanitization
    $username = trim(mysqli_real_escape_string($dbConn,$_POST['username']));
    $fname = trim(mysqli_real_escape_string($dbConn,$_POST['fname']));
    $lname = trim(mysqli_real_escape_string($dbConn,$_POST['lname']));
    $dob = trim($_POST['dob']);
    if(empty($username)){ $errors[] = $usernameError = "";}
    if(empty($fname)){ $errors[] = $fnameError = "";}
    if(empty($lname)){ $errors[] = $lnameError = "";}
    if(empty($dob)){ $errors[] = $dobError = "";}
    if(isset($_POST['tags']) && count($_POST['tags']) > 0){
        $tags = json_encode($_POST['tags']);
    }
    else{
        $errors[] = $tagError = "";
        $error = "please select atleast 1 tag";
    }
    
    # data validation
    if(!empty($username)){
        if(strlen($username) > 15){
            $errors[] = $usernameError = "";
            $error = "maximum of 15 letters expected";
        }
        elseif(strpos($username, " ")){
            $errors[] = $usernameError = "";
            $error = "Username cannot contain space";
        }
        else{
            # prevent duplicate entry
            $query = mysqli_query($dbConn,"SELECT username FROM personnels WHERE username='$username'");
            if(mysqli_num_rows($query) >0){
                $errors[] = $usernameError = "";
                $error = "personnel with username '$username' already exists, please provide a unique personnel username";
            }
        }
    }else{
        $errors[] = $usernameError = "";
    }
    
    
//    
//    if($tag == 0){
//        $errors[] = $tagError = "";
//    }
    
    #send data to database 
    if(count($errors) == 0){
        $query = mysqli_query($dbConn,"
        INSERT INTO personnels(username, fname, lname, dob, tags, dc)
        VALUES('$username', '$fname', '$lname', '$dob', '$tags','$now')
        ");
        if($query){
            $success = "personnel '$username' added successfuly";
        }
        else{
            $error = "something went wrong".mysqli_error($dbConn);
        }
    }
    else{
        $errorCnt = count($errors);
        $error = (!empty($error)) ? "You have ($errorCnt) unresolved error(s)<br>".$error : "You have ($errorCnt) unresolved error(s)";
        } 
    }
    else{
    $error = "kindly check the consent box to proceed";
    }
}

// delete personnel
if(isset($_GET['delete']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn,"
    DELETE FROM personnels WHERE id=$id
    ");
    if($query){
        $success = "personnel $id deleted successfully";
        header("Refresh: 5; url=personnels.php");
    }
    else{
        $error = "something went wrong".mysqli_error($dbConn);
        }
}


// edit personnel
if(isset($_GET['edit']) && isset($_GET['id'])){
        $id = $_GET['id'];
        if(isset($_POST['usernameEdit'])){
           $usernameEdit = trim(mysqli_real_escape_string($dbConn,$_POST['usernameEdit']));
           $fnameEdit = trim(mysqli_real_escape_string($dbConn,$_POST['fnameEdit']));
           $lnameEdit = trim(mysqli_real_escape_string($dbConn,$_POST['lnameEdit']));
           $dobEdit = trim(mysqli_real_escape_string($dbConn,$_POST['dobEdit']));
            if(empty($usernameEdit)){ $errors[] = $usernameEditError = "";}
            elseif(empty($fnameEdit)){ $errors[] = $fnameEditError = "";}
            elseif(empty($lnameEdit)){ $errors[] = $lnameEditError = "";}
            elseif(empty($dobEdit)){ $errors[] = $dobEditError = "";}
            else{
                 # prevent duplicate entry
            $query = mysqli_query($dbConn,"SELECT * FROM personnels WHERE username='$usernameEdit' AND id<>$id");
            if(mysqli_num_rows($query) >0){
                $errors[] = $usernameEditError = "";
                $error = "Username already taken";
            }
                
        }
        
        #send data to database 
    if(count($errors) == 0){
        $query = mysqli_query($dbConn,"UPDATE personnels SET username='$usernameEdit', fname='$fnameEdit', lname='$lnameEdit', dob='$dobEdit', du='$now' WHERE id=$id");
        if($query){
            $success = "personnel information updated succeefully";
        }
        else{
            $error = "something went wrong".mysqli_error($dbConn);
        }
    }
    else{
        $errorCnt = count($errors);
        $error = (!empty($error)) ? "You have ($errorCnt) unresolved error(s)<br>".$error : "You have ($errorCnt) unresolved error(s)";
        } 
     }
    if(isset($_POST['updateTag'])){
            if(isset($_POST['tagsEdit']) && count($_POST['tagsEdit']) >0){
            $formTags = $_POST['tagsEdit'];
           $tagsEdit = json_encode($formTags);
        
                 # prevent duplicate entry
            $query = mysqli_query($dbConn,"SELECT tags FROM personnels WHERE id=$id");
            if(mysqli_num_rows($query) > 0){
                $dbPersonnel = mysqli_fetch_assoc($query);
                $dbTags = json_decode($dbPersonnel['tags'], true);
                $valueDifference = array_diff($formTags,$dbTags);
                if(count($valueDifference) == 0) {
                $errors[] = $tagsEditError = "";
                $error = " please modify the tag(s) to continue";
                }
            }
                
        }
        else{
            $errors[] = $tagsEditError = "";
            $error = " please select at ta g atleast";
        }
        
        #send data to database 
    if(count($errors) == 0){
        $query = mysqli_query($dbConn,"UPDATE personnels SET tags='$tagsEdit', du='$now' WHERE id=$id");
        if($query){
            $success = "tag updated  succeefully";
        }
        else{
            $error = "something went wrong".mysqli_error($dbConn);
        }
    }
    else{
        $errorCnt = count($errors);
        $error = (!empty($error)) ? "You have ($errorCnt) unresolved error(s)<br>".$error : "You have ($errorCnt) unresolved error(s)";
        } 
     }
    
        
}

//truncate table
if(isset($_GET['truncate'])){
    $prompt = true;
    $promptTitle = "Confirm Truncate";
    $promptMsg = "Are you sure you want to truncate the entire 'Personnels' table?";
    $buttonText = "Yes, proceed";
    if(isset($_POST['doPrompt'])){
        $prompt = false;
        $query = mysqli_query($dbConn,"TRUNCATE personnels");
        if($query){
            $success = "All table data cleared successfuly";
            header("Refresh: 4; url=$url");
        }
        else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
    }
}

//activate table
if(isset($_GET['activate'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn,"UPDATE personnels SET status='active' WHERE id=$id");
    if($query){
        $success = "activation of the personnel was successful";
        header("Refresh: 4; url=$url");
        
    }else{
        $error = "something went wrong".mysqli_error($dbConn);
    }
}

//deactivate table
if(isset($_GET['deactivate'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn,"UPDATE personnels SET status='inactive' WHERE id=$id");
    if($query){
        $success = "deactivation of the personnel was successful";
        header("Refresh: 4; url=$url");
        
    }else{
        $error = "something went wrong".mysqli_error($dbConn);
    }
}



if(isset($_POST['multiDelete'])){
    if(!isset($_GET['edit'])){
        if(isset($_POST['personnelIds']) && count($_POST['personnelIds']) > 0){
            $ids = $_POST['personnelIds'];
            $idCnt = count($ids);
            if($idCnt < 6 && $idCnt >0){
                foreach($ids as $id){
                   $query = mysqli_query($dbConn,"DELETE FROM personnels WHERE id=$id");
                    $queryResults[] = $query;
             }
             if (count($queryResults) > 0){
                 $falseFeedback = 0;
                 foreach($queryResults as $result){
                     if(!$result){ $falseFeedback++; }
                 }
                 if($falseFeedback > 0 && $falseFeedback < $idCnt){
                     $success = "Action successful but not all entries were deleted";
                 }
                 elseif($falseFeedback == $idCnt){
                     $error = "Something went wrong, please try agian later";
                 }
                 else{
                     $success = "selected records were deleted successfuly";
                 }
             }  
            }
            else{
                $error = "maximum of 5 rows  can be deleted at a <strong>GO</strong>";
            }
        }
        else{
            $error = "please select at least one (1) record using the check box to proceed";
        }
    }
}

const TITLE = "Manage Personnels";
const HEADER = "Personnels";
const PAGE_LINK = "personnels";
    
include "includes/head.php";
?>




<div class="container-fluid">
    <div class="row my-2 mx-1 gx-0">
        <?php include"includes/aside.php"?>
        <div class="col-md-10">
            <?php include"includes/header.php"?>
            <div class="content border-top border-dark border-2 p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">New Personnel</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset>
                                        <legend class="fs-sm">Personnel Information</legend>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person text-theme"></i></span>

                                            <input type="text" placeholder="Username" name="username" class="form-control <?= isset($usernameError) ? 'border-danger' : '' ?>" value="<?php if($clearFields){ echo ''; }elseif(isset($_POST['username'])){ echo $_POST['username']; }else{ echo ''; }?>">

                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person text-theme"></i></span>

                                            <input type="text" placeholder="Firstname" name="fname" class="form-control <?= isset($lnameError) ? 'border-danger' : '' ?>" value="<?php if($clearFields){ echo ''; }elseif(isset($_POST['fname'])){ echo $_POST['fname']; }else{ echo ''; }?>">

                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person text-theme"></i></span>

                                            <input type="text" placeholder="Lastname" name="lname" class="form-control <?= isset($fnameError) ? 'border-danger' : '' ?>" value="<?php if($clearFields){ echo ''; }elseif(isset($_POST['lname'])){ echo $_POST['lname']; }else{ echo ''; }?>">

                                        </div>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-calendar3 text-theme"></i></span>

                                            <input type="date" name="dob" class="form-control <?= isset($usernameError) ? 'border-danger' : '' ?>" value="<?php if($clearFields){ echo ''; }elseif(isset($_POST['dob'])){ echo $_POST['dob']; }else{ echo ''; }?>">

                                        </div>

                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-list-ul  text-theme"></i></span>
                                            <select name="tags[]" multiple id="" class="form-select <?= isset($tagError) ? 'border-danger' : '' ?>">
                                                <option value="">select personnel</option>
                                                <?php
                                                
                                            $query = mysqli_query($dbConn, "SELECT * FROM tags");
                                            while($record = mysqli_fetch_assoc($query)):
                                                ?>
                                                <option <?= isset($_POST['tag']) && $_POST['tag'] == $record['id'] ? 'selected' : '' ?> value="<?= $record['id'] ?>"><?= $record['name'] ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="">
                                                <input type="checkbox" class="form-check-inline" name="iAgree">I agree to proceed
                                            </label>
                                        </div>
                                        <div class="">
                                            <button class="btn btn-success rounded-pill" name="addPersonnel"><i class="bi-check-circle me-2"></i>Add</button>
                                            <button type="reset" class="btn btn-outline-danger rounded-pill"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                                        </div>
                                    </fieldset>
                                </form>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <form action="" method="post">
                            <div class="card shadow border-0">
                                <div class="card-header bg-theme">
                                    <h4 class="mb-0 card-title">Manage Personnels</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive overflow-auto">


                                        <table class="table table-stripped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><i class="bi bi-list-ol text-secondary"></i></th>
                                                    <th><i class="bi bi-check-square"></i></th>
                                                    <th><i class="bi bi-toggles2"></i></th>
                                                    <th>Details</th>
                                                    <th>Tags</th>
                                                    <th scope="col"><i class="bi bi-calendar text-theme"></i></th>
                                                    <th scope="col"><i class="bi bi-calendar3 text-primary"></i></th>
                                                    <th scope="col"><i class="bi bi-toggle-on text-success"></i></th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                            $no = 1;
                                            $query = mysqli_query($dbConn, "SELECT * FROM personnels");
                                            while($record = mysqli_fetch_assoc($query)){
                                                $tags = json_decode($record['tags'],true);
                                                
                                            ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>

                                                        <input type="checkbox" name="personnelIds[]" value="<?= $record['id'] ?>">

                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <?php if($record['status'] != 'active'): ?>
                                                            <a href="personnels.php?activate&id=<?= $record['id']?>
                                                        " title="Activate" class="btn btn-sm btn-success"><i class="bi bi-check"></i></a>
                                                            <?php else: ?>
                                                            <a href="personnels.php?deactivate&id=<?= $record['id']?>
                                                        " title="Deactivate" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i></a>
                                                            <?php if(isset($_GET['edit']) && $_GET['id'] == $record['id']) :?>
                                                            <a href="personnels.php
                                                        " title="Cancel Edit <?= $record['username'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x"></i></a>
                                                            <?php else : ?>
                                                            <a href="personnels.php?edit&id=<?= $record['id']?>
                                                        " title="Edit <?= $record['username'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill"></i></a>
                                                            <?php endif?>
                                                            <a href="personnels.php?delete&id=<?= $record['id']?>
                                                        " title="Delete <?= $record['username'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></a>
                                                            <?php endif?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                        <ul class="list-group">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person" title="username"></i></span>
                                                            <div class="input-group ms-2">
                                                               <input type="text" name="usernameEdit" class="form-control <?= isset($usernameEditError) ? 'border-danger' : '' ?>" value="<?= (isset($_POST['usernameEdit'])) ? $_POST['usernameEdit'] : $record['username'] ?>">
                                                            </div>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person-workspace"></i></span>
                                                            <div class="input-group ms-2">
                                                               <input type="text" name="fnameEdit" class="form-control <?= isset($fnameEditError) ? 'border-danger' : '' ?>" value="<?= (isset($_POST['fnameEdit'])) ? $_POST['fnameEdit'] : $record['fname'] ?>">
                                                            </div>   
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person-rolodex"></i></span>
                                                            <div class="input-group ms-2">
                                                               <input type="text" name="lnameEdit" class="form-control <?= isset($lnameEditError) ? 'border-danger' : '' ?>" value="<?= (isset($_POST['lnameEdit'])) ? $_POST['lnameEdit'] : $record['lname'] ?>">
                                                            </div>   
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-calendar3"></i></span>
                                                            <div class="input-group ms-2">
                                                               <input type="date" name="dobEdit" class="form-control <?= isset($dobEditError) ? 'border-danger' : '' ?>" value="<?= (isset($_POST['dobEdit'])) ? $_POST['dobEdit'] : $record['dob'] ?>">
                                                            </div>   
                                                            </li>
                                                        </ul>


                                                        </form>
                                                        <?php else: ?>
                                                        <ul class="list-group">
                                                            <li class="list-group-item justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person"></i></span>
                                                            <?= $record['username'] ?>
                                                            </li>
                                                            <li class="list-group-item justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person-workspace"></i></span>
                                                            <?= $record['fname'] ?>
                                                            </li>
                                                            <li class="list-group-item justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-person-rolodex"></i></span>
                                                            <?= $record['lname'] ?>
                                                            </li>
                                                            <li class="list-group-item justify-content-between align-items-center"><span class="badge bg-theme rounded-pill"><i class="bi bi-calendar3"></i></span>
                                                            <?= $record['dob'] ?>
                                                            </li>
                                                        </ul>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                            <div class="input-group">
                                                                <select name="tagsEdit[]" id="" class="form-select <?= isset($tagsEditError) ? 'border-danger' : '' ?>" multiple>
                                                                    <option value="">select personnel</option>
                                                                    <?php
                                                
                                            $query3 = mysqli_query($dbConn, "SELECT * FROM tags");
                                            while($record2 = mysqli_fetch_assoc($query3)):
                                                ?>
                                                                    <option <?= in_array($record2['id'],$tags) ? 'selected' : '' ?> value="<?= $record2['id'] ?>"><?= $record2['name'] ?></option>
                                                                    <?php endwhile; ?>
                                                                </select>
                                                                <button class="btn btn-primary" type="submit" name="updateTag">
                                                                    <i class="bi bi-check"></i>
                                                                </button>
                                                            </div>
                                                        </form>
                                                        <?php else: ?>
                                                        <p><?= implode(" - ",$tags) ?></p>
                                                        <?php endif ?>
                                                    </td>
                                                    <td><?= $record['dc'] ?></td>
                                                    <td><?= $record['du'] ?></td>
                                                    <td><i class="bi bi-circle-fill text-<?= ($record['status'] == 'active') ? 'success' : 'secondary' ?>"></i></td>
                                                </tr>
                                                <?php } ?>


                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group">
                                        <a href="personnels.php?truncate" class="btn btn-outline-warning" title="Truncate Table">
                                            <i class="bi bi-trash2-fill"></i></a>
                                        <button type="submit" name="multiDelete" class="btn btn-outline-danger" title="Delete Multiple">
                                            <i class="bi bi-trash3-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include"includes/foot.php"?>