<?php
include "includes/config.php";
$url = "quotes.php";

#page logics
//Add Quote
if(isset($_POST['addQuote'])){
    if(isset($_POST['iAgree'])){
          #data collection & sanitization
    $quote = trim(mysqli_real_escape_string($dbConn,$_POST['quote']));
    if(isset($_POST['tags']) && count($_POST['tags']) > 0){
        $tags = json_encode($_POST['tags']);
    }
    else{
        $errors[] = $tagError = "";
        $error = "please select atleast 1 tag";
    }
    
    # data validation
    if(!empty($quote)){
        if(strlen($quote) > 500){
            $errors[] = $quoteError = "";
            $error = "maximum of 500 letters expected";
        }
        else{
            
                if(strlen($quote) > 20){ $quoteText = substr($quote,0,30)."..."; }else{ $quoteText = $quote; }
            # prevent duplicate entry
            $query = mysqli_query($dbConn,"SELECT details FROM quotes WHERE details='$quote'");
            if(mysqli_num_rows($query) >0){
                $errors[] = $quoteError = "";
                $error = "quote '$quoteText' already exists, please provide a unique quote name";
            }
        }
    }else{
        $errors[] = $quoteError = "";
    }
    
    
//    
//    if($tag == 0){
//        $errors[] = $tagError = "";
//    }
    
    #send data to database 
    if(count($errors) == 0){
        $query = mysqli_query($dbConn,"
        INSERT INTO quotes(details,tags,dc)
        VALUES('$quote','$tags','$now')
        ");
        if($query){
            $success = "quote '$quoteText' added successfuly";
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

// delete quote
if(isset($_GET['delete']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn,"
    DELETE FROM quotes WHERE id=$id
    ");
    if($query){
        $success = "quote $id deleted successfully";
        header("Refresh: 5; url=quotes.php");
    }
    else{
        $error = "something went wrong".mysqli_error($dbConn);
        }
}


// edit quote
if(isset($_GET['edit']) && isset($_GET['id'])){
        $id = $_GET['id'];
        if(isset($_POST['updateDetails'])){
           $quote = trim(mysqli_real_escape_string($dbConn,$_POST['detailsEdit']));
            if(empty($quote)){
               $errors[] = $quoteEditError = "";
            }
            else{
                 # prevent duplicate entry
            $query = mysqli_query($dbConn,"SELECT details FROM quotes WHERE details='$quote' AND id=$id");
            $query2 = mysqli_query($dbConn,"SELECT details FROM quotes WHERE details='$quote' AND id<>$id");
            if(mysqli_num_rows($query) >0){
                $errors[] = $quoteEditError = "";
                $error = " please modify the name to continue";
            }
            if(mysqli_num_rows($query2) >0){
                $errors[] = $quoteError = "";
                $error = "quote '$quote' already exists in the database";
            }
                
        }
        
        #send data to database 
    if(count($errors) == 0){
        $query = mysqli_query($dbConn,"UPDATE quotes SET details='$quote', du='$now' WHERE id=$id");
        if($query){
            $success = "quote updated to '$quote'  succeefully";
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
            $query = mysqli_query($dbConn,"SELECT tags FROM quotes WHERE id=$id");
            if(mysqli_num_rows($query) > 0){
                $dbQuote = mysqli_fetch_assoc($query);
                $dbTags = json_decode($dbQuote['tags'], true);
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
        $query = mysqli_query($dbConn,"UPDATE quotes SET tags='$tagsEdit', du='$now' WHERE id=$id");
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
    $promptMsg = "Are you sure you want to truncate the entire 'Quotes' table?";
    $buttonText = "Yes, proceed";
    if(isset($_POST['doPrompt'])){
        $prompt = false;
        $query = mysqli_query($dbConn,"TRUNCATE quotes");
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
    $query = mysqli_query($dbConn,"UPDATE quotes SET status='active' WHERE id=$id");
    if($query){
        $success = "activation of the quote was successful";
        header("Refresh: 4; url=$url");
        
    }else{
        $error = "something went wrong".mysqli_error($dbConn);
    }
}

//deactivate table
if(isset($_GET['deactivate'])){
    $id = $_GET['id'];
    $query = mysqli_query($dbConn,"UPDATE quotes SET status='inactive' WHERE id=$id");
    if($query){
        $success = "deactivation of the quote was successful";
        header("Refresh: 4; url=$url");
        
    }else{
        $error = "something went wrong".mysqli_error($dbConn);
    }
}



if(isset($_POST['multiDelete'])){
    if(isset($_POST['quoteIds']) && count($_POST['quoteIds']) > 0){
        $ids = $_POST['quoteIds'];
        $idCnt = count($ids);
        if($idCnt < 6 && $idCnt >0){
            foreach($ids as $id){
               $query = mysqli_query($dbConn,"DELETE FROM quotes WHERE id=$id");
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
//        $error = "please select at least one (1) record using the check box to proceed";
    }
}

const TITLE = "Manage Quotes";
const HEADER = "Quotes";
const PAGE_LINK = "quotes";
    
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
                                <h4 class="mb-0 card-title">New Quote</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <fieldset>
                                        <legend class="fs-sm">Quote Information</legend>
                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-quote-ambiguous text-theme"></i></span>
                                            <textarea name="quote" maxlength="500" id="" cols="30" rows="10" class="form-control <?= isset($quoteError) ? 'border-danger' : '' ?>"><?= $_POST['quote'] ?? '' ?></textarea>
                                        </div>

                                        <div class="input-group mb-4">
                                            <span class="input-group-text"><i class="bi bi-list-ul  text-theme"></i></span>
                                            <select name="tags[]" multiple id="" class="form-select <?= isset($tagError) ? 'border-danger' : '' ?>">
                                                <option value="">select quote</option>
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
                                            <button class="btn btn-success rounded-pill" name="addQuote"><i class="bi-check-circle me-2"></i>Add</button>
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
                                    <h4 class="mb-0 card-title">Manage Quotes</h4>
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
                                            $query = mysqli_query($dbConn, "SELECT * FROM quotes");
                                            while($record = mysqli_fetch_assoc($query)){
                                                $tags = json_decode($record['tags'],true);
                                                
                                            ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>

                                                        <input type="checkbox" name="quoteIds[]" value="<?= $record['id'] ?>">

                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <?php if($record['status'] != 'active'): ?>
                                                            <a href="quotes.php?activate&id=<?= $record['id']?>
                                                        " title="Activate" class="btn btn-sm btn-success"><i class="bi bi-check"></i></a>
                                                            <?php else: ?>
                                                            <a href="quotes.php?deactivate&id=<?= $record['id']?>
                                                        " title="Deactivate" class="btn btn-sm btn-secondary"><i class="bi bi-x"></i></a>
                                                            <?php if(isset($_GET['edit']) && $_GET['id'] == $record['id']) :?>
                                                            <a href="quotes.php
                                                        " title="Cancel Edit <?= $record['details'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-x"></i></a>
                                                            <?php else : ?>
                                                            <a href="quotes.php?edit&id=<?= $record['id']?>
                                                        " title="Edit <?= $record['details'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill"></i></a>
                                                            <?php endif?>
                                                            <a href="quotes.php?delete&id=<?= $record['id']?>
                                                        " title="Delete <?= $record['details'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></a>
                                                            <?php endif?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                            <div class="input-group">
                                                                <textarea name="detailsEdit" id="" cols="30" rows="5" class="form-control <?= isset($quoteEditError) ? 'border-danger' : '' ?>">
                                                                <?= (isset($_POST['detailsEdit'])) ? $_POST['detailsEdit'] : $record['details'] ?>  
                                                            </textarea>
                                                                <button class="btn btn-primary" type="submit" name="updateDetails">
                                                                    <i class="bi bi-check"></i>
                                                                </button>
                                                            </div>


                                                        </form>
                                                        <?php else: ?>
                                                        <i class="bi bi-chat-text-fill text-fill" title="<?= $record['details'] ?>"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($_GET['edit']) && (isset($_GET['id']) && $_GET['id'] == $record['id'])): ?>
                                                        <form action="" method="post">
                                                            <div class="input-group">
                                                                <select name="tagsEdit[]" id="" class="form-select <?= isset($tagsEditError) ? 'border-danger' : '' ?>" multiple>
                                                                    <option value="">select quote</option>
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
                                        <a href="quotes.php?truncate" class="btn btn-outline-warning" title="Truncate Table">
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