<?php
  
  
  include ("classes/autoloader.php");



  $login = new Login();
  $user_data = $login->check_login($_SESSION['Bisuconnect_stud_ID']);
  $Post = new Post();
  $ROW = false;

  // To go back to a page where you came from
  if(isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], "single_post_profile.php")){
    $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
  }

  if($_SERVER['REQUEST_METHOD'] == "POST" ) //inserting post to db
  {

      $post = new Post();
      $id = $_SESSION['Bisuconnect_stud_ID'];
      $result = $post->create_post($id, $_POST,$_FILES);

      //To avoid resubmission of post when refreshing

      if($result == ""){
        header("Location: single_post_profile.php?id=$_GET[id]");
        die;
      }else{

      }
    
  }


  // show who comment in the  posts

  $ERROR = "";
  if(isset($_GET['id'])){

    $ROW = $Post->get_one_posts($_GET['id']);

  }
  else{
    $ERROR = "No data was found!";
  }

  
  
  //change image in sidebar
    $corner_image = "images/bman1.jpg";

    if(isset($user_data))
    {
      $corner_image = $user_data['profile_image'];
    }

  
?>

<!--=== HTML ===-->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>BISUconnect | Comments </title>
    <link rel="stylesheet" href="style/style_comment_t.css">

    <link rel="shortcut icon" type="x-icon" href="images/logo.png">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
   </head>

<body>
 <!-- MOBILE NAVBAR -->
 <!-- White separator bar -->
<div class="mobile-separator"></div>
 <div class="mobile-navbar">
  <ul>
    <li class="list active">
      <a  href="index.php">
        <span class="mobile-icon">
          <i class='bx bx-news' ></i>
        </span>
        <span class="mobile-txt">Home</span>
      </a>
      </span>
    </li>
    <li class="list">
      <a href="#">
        <span class="mobile-icon">
          <i class='bx bx-chat' ></i>
        </span>
        <span class="mobile-txt">Messages</span>
      </a>
      </span>
    </li>
    <li class="list">
      <a href="Profile_page.php">
        <span class="mobile-icon">
          <i class='bx bx-user' ></i>
        </span>
        <span class="mobile-txt">Profile</span>
      </a>
      </span>
    </li>
    <li class="list">
      <a href="#">
        <span class="mobile-icon">
          <i class='bx bx-cog' ></i>
        </span>
        <span class="mobile-txt">Settings</span>
      </a>
      </span>
    </li>
    <li class="list">
      <a href="P_logout.php">
        <span class="mobile-icon">
          <i class='bx bx-log-out' ></i>
        </span>
        <span class="mobile-txt">Logout</span>
      </a>
      </span>
    </li>
    <div class="indicator"></div>
  </ul>
</div>
<!--===END OF MOBILE NAVBAR ===-->

 
  <!--COVER AREA -->
  <div class="container">
      <!--LEFT SIDEBAR-->
      <?php include("Sidebar.php"); ?>
      <!-- END OF SIDEBAR-->


    <div class="profile-section">

        <!-- below cover-->
       <!-- pop-up modal for delete post-->       
       <div class="delete-modal">

         <nav class="home-header"> 
            <h3><a href="index.php" style="color: #212145; "> BisuConnect</a></h3>    
          </nav>

            <div class="delete-content">
            <div class="close-del">
              <?php if($ROW['parent'] == 0){ ?>
                <a href="Profile_page.php?id=<?php echo $ROW['stud_ID'];  ?>" id="closeButton">+</a>
              <?php }else{ ?>
                <a href="single_post_profile.php?id=<?php echo $ROW['parent'] ?>" id="closeButton">+</a>
              <?php }?>               
            </div>
            <?php if($ROW['parent'] != 0){ ?>
                <h2> Replies</h2> 
            <?php }else{ ?>
              <h2> Comments</h2> 

            <?php } ?>
              <?php

                //check if this is from a notification

                if(isset($_GET['notif'])){
                  notification_seen($_GET['notif']);
                }
                


                $user = new User();
                $image_class = new Image();

                if(is_array($ROW)){
                  
                  $ROW_user = $user->get_user($ROW['stud_ID']);

                  if(is_array($ROW)){
                  
                    $ROW_user = $user->get_user($ROW['stud_ID']);
  
                    if($ROW['parent'] == 0){
                      Include("P_post_comment_profile.php");  
                    }else{
                      $COMMENT = $ROW;
                      Include("comment_profile.php"); 
                    }              
                    
                  }              
                  
                }
              
              ?> <!-- view original post -->
              <?php if($ROW['parent'] != 0){ ?>
                <a href="single_post_profile.php?id=<?php echo $ROW['parent'] ?>">
                  <input id="up" type="submit" value="View original post"> 
                </a>
              <?php } ?>

            <!-- comment area -->
              <hr style="width:100%">
              <div class="posts-area">
                 <form method="post" enctype="multipart/form-data" action="#">
                 <?php if($ROW['parent'] == 0){ ?>
                    <textarea name="post" id="text-post" placeholder="Comment Something . . ."></textarea>
                        <?php }else{ ?>
                    <textarea name="post" id="text-post" placeholder="Reply Something . . ."></textarea>
                        <?php } ?>
                    
                    <input name="parent" id="posts_browse" type="hidden" value="<?php  echo $ROW['post_id']  ?> "> <!-- Specify accepted file types -->

                    <!-- Accepting image comment -->
          <!--      <input name="file" id="posts_browse" type="file" accept="image/jpeg, image/png">  -->
                    <?php if($ROW['parent'] == 0){ ?>
                      <input id="posts_btn" type="submit" value="Comment">
                    <?php }else{ ?>
                      <input id="posts_btn" type="submit" value="Reply">
                    <?php } ?>
                    
                    
                  </form>

              
              
              
                  <!-- Get comments -->
              <?php

                $comments = $Post->get_comments($ROW['post_id']);

                if(is_array($comments)){

                  foreach ($comments as $COMMENT){

                    Include("comment_profile.php");
                  }
                }else{
                  // No comments, display "No more comments" message
                  echo "<div class='noComment'>";
                  echo "No more comments";
                  echo "</div>";
                }
              ?>
            </div>  
        </div>
    </div>


    <?php
            //pagination on previous and next page on class_F_paginationLink.php
            $pg = pagination_link();
        ?>   
           
        <div class="page-container">
                        <a href="<?php echo $pg['prev_page'] ?>"> 
                            <i class='bx bxs-chevron-left' style="color: #f0c310"></i>
                            <?php if($ROW['parent'] == 0){ ?>
                              <input id="page" type="submit" value="Recent Comments"> 
                            <?php }else{ ?>
                              <input id="page" type="submit" value="Recent Replies"> 
                            <?php } ?>
                            
                        </a>

                        <a href="<?php echo  $pg['next_page'] ?>"> 
                            <?php if($ROW['parent'] == 0){ ?>
                              <input id="page" type="submit" value="More Comments"> 
                            <?php }else{ ?>
                              <input id="page" type="submit" value="More Replies"> 
                            <?php } ?>
                            <i class='bx bxs-chevron-right' style="color: #f0c310"></i>
                        </a>
                    </div>

                    
  <!--=== Mobileview Bar Script==-->
  <script>
    const list = document.querySelectorAll('.list');
    function activeLink(){
      list.forEach((item) =>
      item.classList.remove('active'));
      this.classList.add('active');
    }
    list.forEach((item) =>
    item.addEventListener('click' , activeLink));
  </script>

  <!--=== Sidebar Script==-->
  <script>
  let sidebar = document.querySelector(".sidebar");
  let closeBtn = document.querySelector("#btn");
  let searchBtn = document.querySelector(".bx-search");

  closeBtn.addEventListener("click", ()=>{
    sidebar.classList.toggle("open");
    menuBtnChange();//calling the function(optional)
  });

  searchBtn.addEventListener("click", ()=>{ // Sidebar open when you click on the search iocn
    sidebar.classList.toggle("open");
    menuBtnChange(); //calling the function(optional)
  });

  // following are the code to change sidebar button(optional)
  function menuBtnChange() {
   if(sidebar.classList.contains("open")){
     closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");//replacing the iocns class
   }else {
     closeBtn.classList.replace("bx-menu-alt-right","bx-menu");//replacing the iocns class
   }
  }

  // Get the current page filename
  var currentPage = window.location.pathname.split('/').pop();

  // Add the 'profilebg' class to the corresponding list item
  if (currentPage === 'people-likes.php') {
    document.getElementById('profile').classList.add('profilebg');
  }else{
    document.getElementById('activity-stream').classList.add('profilebg');
  }
  </script>


    <!--=== Script for loading ====-->

    <script>
        $(window).on('load', function(){
          $(".spinner-parent").fadeOut(1010);
          $(".body").fadeIn(1010);
        })
    </script>



<!-- using AJAX to not refresh the page when clicking like -->
<script>
// Function to check if the post is liked in local storage
function isPostLiked(postId) {
    return localStorage.getItem('liked_post_' + postId) === 'true';
}

// Function to handle liking a post
function like_post(e, postId) {
    e.preventDefault();

    // Toggle the 'liked' class on the parent 'a' element
    e.target.parentNode.classList.toggle('liked');

    // Save the liked status in local storage
    var isLiked = e.target.parentNode.classList.contains('liked');
    localStorage.setItem('liked_post_' + postId, isLiked);

    // Send the request to the server using AJAX
    var link = "like.php?type=post&id=" + postId;
    var xml = new XMLHttpRequest();

    xml.onreadystatechange = function () {
        if (xml.readyState == 4 && xml.status == 200) {
            // Update the like count
            var likesCount = parseInt(xml.responseText);
            updateLikeCount(postId, likesCount);
        }
    };

    xml.open("GET", link, true);
    xml.send();
}

// Function to update like count in the DOM
function updateLikeCount(postId, count) {
    var likeCountElement = document.getElementById('like-count-' + postId);

    if (likeCountElement) {
        var text = "";

        if (count > 0) {
            if (count === 1) {
                text = "1 person loved this ";
            } else {
                text = count + " people loved this ";
            }
        }

        likeCountElement.textContent = text;
    }
}

// Check liked status on page load and apply 'liked' class
document.addEventListener('DOMContentLoaded', function () {
    var likeElements = document.querySelectorAll('.heart');
    
    likeElements.forEach(function (likeElement) {
        var postId = likeElement.getAttribute('data-post-id');
        var isLiked = isPostLiked(postId);
        
        if (isLiked) {
            likeElement.classList.add('liked');
        }
    });
});
</script>
  


</body>
</html>