<?php
include "includes/config.php";
 $url = "roles.php";

# PAGE LOGICS

// ADD GENDERS

if(isset($_POST['addRole'])){
    if(isset($_POST['iAgree'])){
            # data collection
        $role = trim(mysqli_real_escape_string($dbConn, $_POST['role']));

        if(!empty($role)){
            if(strlen($role) > 25){
                $errors[] = $roleError = "";
                $error = "maximum of 25 letters expected";
            }
            else{
                #prevent duplicate entry
                $query = mysqli_query($dbConn, "SELECT name FROM roles WHERE name='$role'");
                if(mysqli_num_rows($query) > 0){
                    $errors[] = $roleError = "";
                    $error = " role '$role' already exists, please modify";
                }
            }
        }
        else{
            $errors[] = $roleError = "";
        }

        //    
        //    if($parent == 0){
        //        $errors[] = $parentError = "";
        //    }

        # send data to database 
        if(count($errors) == 0){
            $query = mysqli_query( $dbConn, "INSERT INTO roles (name, dc) VALUES('$role', '$now') ");
            if($query){
                $success = "role '$role' added successfully";
                $clearFields = true;
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
        $error = "Kindly check the conset box to proceed";
    }
}

# Delete role
if(isset($_GET['delete']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn, "DELETE FROM roles WHERE id=$id");
    if($query){
        $success = " Role $id deleted successfully";
        header("Refresh: 5; url=roles.php");
    }
    else{
        $error = "something went wrong".mysqli_error($dbConn);  
    }
}
# Edit role
if(isset($_GET['edit']) && isset($_GET['id'])){
    $id = $_GET['id'];
    if(isset($_POST['nameEdit'])){
        $role = trim(mysqli_real_escape_string($dbConn, $_POST['nameEdit']));
        if(empty($role)){
            $errors[] = $roleEditError = "";
        }
        else{
            #prevent duplicate entry
            $query = mysqli_query($dbConn, "SELECT name FROM roles WHERE name='$role' AND id=$id");
            $query2 = mysqli_query($dbConn, "SELECT name FROM roles WHERE name='$role' AND id<>$id");
            if(mysqli_num_rows($query) > 0){
                $errors[] = $roleEditError = "";
                $error = " Please modify the name to continue";
            }
            if(mysqli_num_rows($query2) > 0){
                $errors[] = $roleEditError = "";
                $error = " role '$role' already exists in the Database";
            }
        } 
        # send data to database 
        if(count($errors) == 0){
            $query = mysqli_query( $dbConn, "UPDATE roles SET name='$role', du='$now' WHERE id=$id");
            if($query){
                $success = "role has been updated to '$role' successfully";
                header("Refresh: 5; url=roles.php");
                
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

# Truncate Table
if(isset($_GET['truncate'])){
    $prompt = true;
    $promptTitle = "Confirm Truncate";
    $promptMsg = "Are you sure you want to truncate the entire 'Roles' Table?";
    $buttonText = "Yes, proceed";
   if(isset($_POST['doPrompt'])){
       $prompt = false;
       $query = mysqli_query($dbConn, "TRUNCATE roles");
       if($query){
           $success = "All table data cleared successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
            
   }
    
}
# activate
if(isset($_GET['activate'])){
    $id = $_GET['id'];
   $query = mysqli_query($dbConn, "UPDATE roles SET status='active' WHERE id='$id'");
       if($query){
           $success = "activation of role done successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
    
}
# Deactivate
if(isset($_GET['deactivate'])){
    $id = $_GET['id'];
   $query = mysqli_query($dbConn, "UPDATE roles SET status='inactive' WHERE id='$id'");
       if($query){
           $success = "deactivation of role done successfully";
           header("Refresh: 4; url=$url");
       }
       else{
            $error = "Something went wrong".mysqli_error($dbConn);
        }
    
}

# MultiDelete
if(isset($_POST['multiDelete'])){
    if(isset($_POST['roleIds']) && count($_POST['roleIds']) > 0){
        $ids = $_POST['roleIds'];
        $idCnt = count($ids);
        if($idCnt < 6 && $idCnt > 0){
            foreach($ids as $id){
                $query = mysqli_query($dbConn, "DELETE FROM roles WHERE id=$id");
                $queryResults[] = $query;
            }
            if(count($queryResults) > 0){
                $falseFeedback = 0;
                foreach($queryResults as $result){
                    if(!$result){ $falseFeedback++; }
                }
                if($falseFeedback > 0 && $falseFeedback < $idCnt){
                    $success = "Action successful but not all entries where deleted";
                } 
                elseif($falseFeedback == $idCnt){
                    $error = "Something went wrong, please try again Later";
                }
                else{
                    $success = "Selected records were deleted successfully";
                }
            }
        }
        else{
            $error = "maximum of 5 rows can be deleted at a <strong>GO</strong>";
        }
    }
    else{
        $error = "Please select at least one (1) record using the checkbox to proceed";
    }
}

const TITLE = "Manage Roles";
const HEADER = "Roles ";
const PAGE_LINK = "roles";

include "includes/head.php";
?>


<div class="container-fluid">
    <div class="row my-2 mx-1 gx-0">
        <?php include "includes/aside.php";?>
        <div class="col-md-10">
            <?php include "includes/header.php";?>
            <div class="content border-top border-dark border-2 p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow border-0">
                            <div class="card-header bg-theme">
                                <h4 class="mb-0 card-title">Add Role</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset class="">
                                        <legend class="legend">Role Information</legend>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-person-badge text-theme"></i></span>
                                            <input type="text" placeholder="role name" name="role" class="form-control <?= isset($roleError) ? 'border-danger' : '' ?>" value=" <?php if($clearFields){ echo ''; }elseif(isset($_POST['role'])){ echo $_POST['role']; }else{ echo '';} ?>">
                                        </div>
                                        <div class="mb-4">
                                            <label for="">
                                                <input type="checkbox" name="iAgree" class="form-check-inline" name="">
                                                I agree to continue
                                            </label>
                                        </div>
                                        <div class="">
                                            <button type="submit" name="addRole" class="btn  btn-success rounded-pill"><i class="bi bi-check-circle me-2"></i>Add</button>
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
                                    <h4 class="mb-0 card-title">Manage Roles</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive overflow-auto">

                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><i class="bi bi-list-ol text-secondary"></i></th>
                                                    <th><i class="bi bi-check-square"></i></th>
                                                    <th><i class="bi bi-toggles2"></i></th>
                                                    <th>Name</th>
                                                    <th scope="col"><i class="bi bi-calendar text-theme"></i></th>
                                                    <th scope="col"><i class="bi bi-calendar2 text-primary"></i></th>
                                                    <th scope="col"><i class="bi bi-toggle-on text-success"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                            $no = 1;
                                            $query = mysqli_query($dbConn, "SELECT * FROM roles");
                                            while($record = mysqli_fetch_assoc($query)){
                                            
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?= $no++ ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" name="roleIds[]" value="<?= $record['id'] ?>">
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <?php if($record['status'] != 'active'): ?>
                                                            <a href="roles.php?activate&id=<?= $record['id'] ?>" class="btn btn-sm btn-success" title="<Activate></Activate>"><i class="bi bi-check"></i></a>
                                                            <?php else:  ?>
                                                            <a href="roles.php?deactivate&id=<?= $record['id'] ?>" class="btn btn-sm btn-secondary" title="Deactivate"><i class="bi bi-x"></i></a>
                                                            <?php if(isset($_GET['edit']) && $_GET['id'] == $record['id']): ?>
                                                            <a href="roles.php" class="btn btn-sm btn-danger" title="Cancel Edit"><i class="bi bi-x"></i></a>
                                                            <?php else: ?>
                                                            <a href="roles.php?edit&id=<?= $record['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit <?= $record['name'] ?>"><i class="bi bi-pencil-fill"></i></a>
                                                            <?php endif ?>
                                                            <a href="roles.php?delete&id=<?= $record['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete <?= $record['name'] ?>"><i class="bi bi-trash3-fill"></i></a>
                                                            <?php endif ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                            <input type="text" name="nameEdit" value="<?= (isset($_POST['nameEdit'])) ? $_POST['nameEdit'] : $record['name'] ?>" class="form-control <?= isset($roleEditError) ? 'border-danger' : '' ?>">
                                                            <input type="submit" hidden>
                                                        </form>
                                                        <?php else: ?>
                                                        <?= $record['name'] ?>
                                                        <?php endif ?>
                                                    </td>
                                                    <td><?= $record['dc'] ?></td>
                                                    <td><?= $record['du'] ?></td>
                                                    <td>
                                                        <i class="bi bi-circle-fill text-<?= ($record['status'] == 'active') ? 'success' : 'secondary' ?>"></i>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group">
                                        <a href="roles.php?truncate" title="Truncate Table" class="btn btn-outline-warning"><i class="bi bi-trash2-fill"></i></a>
                                        <button type="submit" name="multiDelete" title="Delete Multiple" class="btn btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include "includes/foot.php";?>
