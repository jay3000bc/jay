<?php
/*
    This class does all form validations for the 'Classes' category
    Objects are created in the post-ad.php script
    which sends all the common parameters AND parameters specific
    to that category and subcategory to the constructor
    Author: Jay@Helix
    Date:08/09/2011
    For any queries mail me : jay3000bc@gmail.com
*/
class saveData{
    private $username;
    private $state;
    private $states;    //States array transfered here
    private $localities;
    private $combined_location;
    private $state_var;
    private $locality;
    private $category;
    private $categories;    //Categories array transfered here
    private $combined_category;
    private $subcategories;
    private $category_var;
    private $subcategory;
    private $ad_type;
    
    private $user_type;
    private $condition;
    private $condition_type;
    private $price;
    private $price_type;
    private $room_type;
    private $rooms;
    private $area_type;
    private $area;
    private $furnished_type;
    private $furnished;
    private $land_type;
    private $land;
    private $car_brand;
    private $car;
    private $motorcycle_brand;
    private $motorcycle;
    private $suv_brand;
    private $suv;
    private $year;
    private $year_make;
    private $mileage_city;
    private $mileage_highway;
    private $fuel;
    private $fuels;
    private $job_type;
    private $salaries;
    private $company;
    private $designation;
    private $education_type;
    private $experience;
    private $skills;
    private $salary;
    
    private $title;
    private $description;
    private $photo_uploads;
    private $embed_video;
    private $email;
    private $mobile;
    private $publish;
    private $alert;
    private $db;
    private $ip;
    private $timestamp;
    private $site_path;
    
    private $photos=array();    //Will finally save the actual photo name
    private $response;  //Returns the response to post-ad
    
    /*  Mandatory Parameters    */
    /*  Common Parameters: state,category,subcategory,title,description,email,mobile    */
    /*  Calasses Category: ad_type    */
    function __construct($username,$state_var,$state,$locality,$category_var,$category,$subcategory,$ad_type,$user_type,$condition_type,$price,$price_type,$room_type,$area_type,$furnished_type,$land_type,$car_brand,$motorcycle_brand,$suv_brand,$year_make,$mileage_city,$mileage_highway,$fuel,$job_type,$company,$designation,$education_type,$experience,$skills,$salary,$title,$description,$photo_uploads,$embed_video,$email,$mobile,$publish,$alert,$ip,$timestamp,$site_path,$db)
    {
        $this->username=$username;
        $this->state=$state;
        $this->state_var=$state_var;
        $this->locality=$locality;
        $this->category=$category;
        $this->category_var=$category_var;
        $this->subcategory=$subcategory;
        $this->ad_type=$ad_type;
        
        $this->user_type=$user_type;
        $this->condition_type=$condition_type;
        $this->price=$price;
        $this->price_type=$price_type;
        $this->room_type=$room_type;
        $this->area_type=$area_type;
        $this->furnished_type=$furnished_type;
        $this->land_type=$land_type;
        $this->car_brand=$car_brand;
        $this->motorcycle_brand=$motorcycle_brand;
        $this->suv_brand=$suv_brand;
        $this->year_make=$year_make;
        $this->mileage_city=$mileage_city;
        $this->mileage_highway=$mileage_highway;
        $this->fuel=$fuel;
        $this->job_type=$job_type;
        $this->salaries=$salaries;
        $this->company=$company;
        $this->designation=$designation;
        $this->education_type=$education_type;
        $this->experience=$experience;
        $this->skills=$skills;
        $this->salary=$salary;
        
        $this->title=$title;
        $this->description=$description;
        $this->photo_uploads=$photo_uploads;
        $this->embed_video=$embed_video;
        $this->email=$email;
        $this->mobile=$mobile;
        $this->publish=$publish;
        $this->alert=$alert;
        $this->ip=$ip;
        $this->timestamp=$timestamp;
        $this->site_path=$site_path;
        $this->db=$db;
    }
    private function photoProcess()
    {
        $photo_uploads_str=explode("{|||###|||}",$this->photo_uploads);
        for($i=1;$i<count($photo_uploads_str);$i++)
        {
            $dot_length=explode(".",$photo_uploads_str[$i]);
            $ext=$dot_length[count($dot_length)-1];
            $photo=$photo_uploads_str[$i];
            
            $slash_length=explode("/",$photo);
            $photo_name=$slash_length[count($slash_length)-1];
            /**
             *Resize photo here
             */
            switch(strtolower($ext))
            {
                case 'jpg':
                            $this_photo=imagecreatefromjpeg($photo);
                            break;
                case 'jpeg':
                            $this_photo=imagecreatefromjpeg($photo);
                            break;
                case 'gif':
                            $this_photo=imagecreatefromgif($photo);
                            break;
                case 'png':
                            $this_photo=imagecreatefrompng($photo);
                            break;
                case 'bmp':
                            $this_photo=imagecreatefromwbmp($photo);
                            break;
                default:
                            break;
            }
            //Creating the Thumb
            $height=imagesy($this_photo);
            $width=imagesx($this_photo);
            if($width>=$height)
            {
                $new_width=120;
                $new_height = $height * ($new_width/$width);
                $new_height=floor($new_height);
            }
            else if($height>=$width)
            {
                $new_height=120;
                $new_width = $width * ($new_height/$height);
                $new_width=floor($new_width);
            }
            /**
            *Start saving the thumbnail here
            */
            $dst_img=ImageCreateTrueColor($new_width,$new_height);
            imagecopyresampled($dst_img,$this_photo,0,0,0,0,$new_width,$new_height,$width,$height);
            imagejpeg($dst_img,"/var/www/html/7sistersonline/thumb-ad-photos/".$photo_name);
            ################################################################################################
            //Creating the large size image
            $height=imagesy($this_photo);
            $width=imagesx($this_photo);
            $wRatio = $width / 660;
            $hRatio = $height / 400;
            $maxRatio = max($wRatio, $hRatio);
            if ($maxRatio > 1)
            {
                $new_width = $width / $maxRatio;
                $new_height = $height / $maxRatio;
            }
            else
            {
                $new_width = $width;
                $new_height = $height;
            }
            /**
            *Start saving the thumbnail here
            */
            $dst_img=ImageCreateTrueColor($new_width,$new_height);
            imagecopyresampled($dst_img,$this_photo,0,0,0,0,$new_width,$new_height,$width,$height);
            imagejpeg($dst_img,"/var/www/html/7sistersonline/ad-photos/".$photo_name);
            ###########################################################################################
            imagedestroy($dst_img); 
            imagedestroy($this_photo);
            
            //$this->photos[$i]=$this->site_path."thumb-ad-photos/".$photo_actual_str[$i].".".$ext;
            $this->photos[$i]=$this->site_path."thumb-ad-photos/".$photo_name;
        }
        $this->photos=addslashes(implode("{{#-#}}",$this->photos));
        return true;
    }
    
    private function sanitizeData()
    {
        //Skills default example check done here
        if($this->skills=="Enter them comma separated. Example: Accounting, Java, Tally, SAP etc.")
            $this->skills="";
            
        //Format Price Type is na, save it as N/A
        if($this->price_type=='na')
        {
            $this->price_type="N/A";
        }
                       
        //Format Condition array into string
        if(count($this->condition_type)>0)
            $this->condition = implode(",",$this->condition_type);
            
        //Format Fuel array into string
        if(count($this->fuel)>0)
            $this->fuels = implode(",",$this->fuel);
            
        //Format Land array into string
        if(count($this->land_type)>0)
            $this->land = implode(",",$this->land_type);
            
        //Housing Swap is a special case
        if($this->subcategory=='Housing Swap')
            $this->ad_type='sell';
            
        //Resizing the Youtube Embed video to 430px x 350px
        $this->embed_video = preg_replace('/(width)=("[^"]*")/i', 'width="430"', $this->embed_video);
        $this->embed_video = preg_replace('/(height)=("[^"]*")/i', 'height="350"', $this->embed_video);
        
        #################################################################################
        #   THIS IS THE MOST CRUCIAL PART - HOW DATA GETS ENTERED INTO THE DATABASE     #
        #   IF YOU WANT TO ENTER DATA AS-IT-IS i.e IN STRING FORMAT                     #
        #   THEN REMOVE THIS PIECE OF BLOCK AND UNCOMMENT THE LOWER BLOCK - BENEAT IT   #
        #################################################################################
        /**
         *We change the State, Locality, Category and Sub-Category, Price (amount), Condition and Ad Type strings to numeric.
         *If you would like to revert it to strings, then remove or comment this block
         */
        include('includes/categories.php');
        include('includes/region.php');
        include('includes/job-options.php');
        $this->states = $states;
        $this->categories = $categories;
        $this->salaries = $salaries;
        $this->combined_location = $this->state.",".$this->locality;
        $this->combined_category = $this->category.",".$this->subcategory;
        
        $states_array=array_keys($this->states);
        $this->localities=$this->states[$this->state];
        $this->state=array_search($this->state,$states_array);  //This will evaluate the State Number (integer)
        
        $categories_array=array_keys($this->categories);
        $this->subcategories=$this->categories[$this->category];
        $this->category=array_search($this->category,$categories_array);  //This will evaluate the Category Number (integer)
        
        $this->locality=array_search($this->locality,$this->localities);    //This will evaluate the Locality Number (integer)
        $this->subcategory=array_search($this->subcategory,$this->subcategories);    //This will evaluate the Subcategory Number (integer)
        
        $price_clean = explode(".",$this->price);
        $this->price = trim(str_replace(",","",$price_clean[0]));   //Formatted price into a clean integer
        
        $this->salary = array_search($this->salary,$this->salaries);    //Fetch the salary key from salaries array
        
        if($this->condition == 'New')
            $this->condition = 1;
        else if($this->condition == 'Used')
            $this->condition = 2;
        else
            $this->condition = 3;
        
        if($this->ad_type == 'sell')
            $this->ad_type = 1;
        else
            $this->ad_type = 2;
        ##########################################################################
        #   IF YOU WANT TO ENTER DATA AS-IT-IS i.e IN STRING FORMAT              #
        #   THEN UNCOMMENT THE CODE JUST UNDER THIS AND REMOVE THE UPPER BLOCK   #
        ##########################################################################
        /**
         *$this->state=addslashes($this->state);
         *$this->locality=addslashes($this->locality);
         *$this->category=addslashes($this->category);
         *$this->subcategory=addslashes($this->subcategory);
         *$this->ad_type=addslashes($this->ad_type);
         *$this->price=addslashes($this->price);
         *$this->condition=addslashes($this->condition);
         *if($this->price!='')
            {
                if(strpos($this->price,","))
                    $this->price=str_replace(",","",$this->price);
                setlocale(LC_MONETARY, "en_IN");
                $this->price = money_format("%i", $this->price);
            }
        */
        
        //Format Price Type is na, save it as N/A
        if($this->price_type=='na')
        {
            $this->price_type="N/A";
        }
        $this->user_type=addslashes($this->user_type);
        
        $this->price_type=addslashes($this->price_type);
        $this->rooms=addslashes($this->room_type);
        $this->area=addslashes($this->area_type);
        $this->furnished=addslashes($this->furnished_type);
        $this->land=addslashes($this->land);
        $this->car=addslashes($this->car_brand);
        $this->motorcycle=addslashes($this->motorcycle_brand);
        $this->suv=addslashes($this->suv_brand);
        $this->year=addslashes($this->year_make);
        $this->fuels=addslashes($this->fuels);
        $this->mileage_city=addslashes($this->mileage_city);
        $this->mileage_highway=addslashes($this->mileage_highway);
        $this->job_type=addslashes($this->job_type);
        $this->company=addslashes($this->company);
        $this->designation=addslashes($this->designation);
        $this->education_type=addslashes($this->education_type);
        $this->experience=addslashes($this->experience);
        $this->skills=addslashes($this->skills);
        $this->salary=addslashes($this->salary);
        
        $this->title=addslashes($this->title);
        $this->description=addslashes($this->description);
        $this->embed_video=addslashes($this->embed_video);
        $this->email=addslashes($this->email);
        $this->mobile=addslashes($this->mobile);
        $this->publish=addslashes($this->publish);
        $this->alert=addslashes($this->alert);
        $this->ip=addslashes($this->ip);
        $this->timestamp=addslashes($this->timestamp);
    }
    public function saveAll()
    {
        include('includes/settings.php');
        
        $active=1;  //1 is Active and 0 is Suspended
        $flag="";
        
        $query="insert into ads(username,state,locality,combined_location,category,subcategory,combined_category,ad_type,user_type,condition_type,price,price_type,rooms,area,furnished,land,car,motorcycle,suv,year_make,fuels,mileage_city,mileage_highway,job_type,company,designation,education,experience,skills,salary,title,description,photo,video,email,mobile,publish,alert,ip,timestamp,active,flag)";
        $query.=" values('$this->username','$this->state','$this->locality','$this->combined_location','$this->category','$this->subcategory','$this->combined_category','$this->ad_type','$this->user_type','$this->condition','$this->price','$this->price_type','$this->rooms','$this->area','$this->furnished','$this->land','$this->car','$this->motorcycle','$this->suv','$this->year','$this->fuels','$this->mileage_city','$this->mileage_highway','$this->job_type','$this->company','$this->designation','$this->education_type','$this->experience','$this->skills','$this->salary','$this->title','$this->description','$this->photos','$this->embed_video','$this->email','$this->mobile','$this->publish','$this->alert','$this->ip','$this->timestamp','$active','$flag')";
        
        $result=$this->db->query($query);
        if($result)
        {
            //$last_id = $this->db->insert_id;
            $this->db->close();
            $_SESSION['email']=$this->email;
            
            /**
             *Send mail to Ad. poster, confirmation that Ad. has been successfully posted
             */
            $ad_manage_link=$this->site_path."manage/".bin2hex($this->email);
            
            $body="Congratulations - Your Ad. has been successfully posted at 7SistersOnline<br /><br />";
            $body.="If you <b>have an account</b> with us, you can manage all your ads. from your account.<br /><br />";
            $body.="If you <b>do not have an account</b>, you can manage your Ads. using this link <a href='".$ad_manage_link."'>".$ad_manage_link."</a> <br /><br />";
            $body.="<hr />";
            $body.="<br />We strongly recommend you to create an account, so that you can manage your Ads. safely and efficiently <a href='".$this->site_path."quick-signup'>Create Account Now!</a><br />";
            $body.="<em><font color='#666666'>If you do not have an account with us, the link sent with this email should be preserved safely. The link is the only access to manage your Ads. Your email is the only identity for all future communications and managing your ads. While posting multiple Ads. we do not recommend you to use different emails.</font></em><br /><br />";
            $body.="Post Ads. &amp; Events for Free <br /><br />";
            $body.=$mail_signature."<br /><br />";
            $body.=$unsubscribe_link;
            
            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            
            // Additional headers
            $headers .= 'To: '.$this->email . "\r\n";
            $headers .= 'From: '.$ad_post_from.' <'.$ad_post_mail_from."> \r\n";
            $headers .= 'Reply-To: '.$reply_to;

            mail($this->email,$ad_post_subject,$body,$headers);
            
            /**
             *Now redirect user to Ad. post Success page
             */
            header("Location:".$this->site_path."ad-post-success");
            exit();
        }
        else
        {
            header("Location:".$site_path."application-error/4410");
            exit();
        }
        
    }
    public function doDataProcess()
    {
        if($this->photo_uploads!='')
            $this->photoProcess();
        else
            $this->photos="";
            
        $this->sanitizeData();
        $this->saveAll();
    }
}
?>