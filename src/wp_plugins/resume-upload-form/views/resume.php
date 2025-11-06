<?php
/**
* Render resume form
*/
function formResume() { 

    global $plugin_url; 

    $captcha = $plugin_url.'helpers/captcha/captcha.php';
    $referesh = $plugin_url.'public/images/referesh.png';   
    
    echo '<div class="container">
        <div class="row ">                    
            <div class="col-md-12 col-lg-offset-2 col-lg-8 py-5">
                <form id="resume_form" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post" enctype="multipart/form-data">
                    <div class="form-row">    
                        <div class="form-group col-md-6">
                            <label>First Name</label> <span class="mark-style">*</span>
                            <input type="text" id="fname" name="fname" value="" class="form-control" />
                        </div>

                        <div class="form-group col-md-6">
                           <label>Last Name</label> <span class="mark-style">*</span>
                            <input type="text" id="lname" name="lname" value="" class="form-control" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                             <label>Email</label> <span class="mark-style">*</span>
                             <input type="email" id="email" name="email" value="" class="form-control" />
                        </div>
                    
                        <div class="form-group col-md-6">
                             <label>Phone</label>
                             <input type="text" id="phone" name="phone" value="" class="form-control" />  
                        </div>
                    </div>                     
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Experience (Years)</label>
                            <input type="number" min="0" step="1" id="exp" name="exp" value="0" class="form-control" />      
                        </div>
                        
                        <div class="form-group col-md-6 upload-btn-wrapper">
                            <label style="width:100%;">Upload your file <span class="mark-style">*</span></label> 
                            <button class="btn btn-dark" style="width:100%;">Select Resume</button>
                            <input type="file" id="file" name="file" class="form-control" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Write To</label>
                            <textarea id="mytextarea" id="comments" name="comments" cols="40" rows="5" class="form-control" style="resize: none;"/></textarea>
                        </div>
                    </div>
                    <div class="form-row"> 
                        <div class="form-group col-md-6">
                            <label style="width:100%; float: left;">Captcha Number</label> 
                            <img id="captchaImg" src="'.$captcha.'" width="160" height="45" border="1" alt="CAPTCHA" />
                            <img id="refreshimg" src="'.$referesh.'" width="50" height="50" />
                        </div>

                        <div class="form-group col-md-6">  
                            <label>Captcha</label> <span class="mark-style">*</span>         
                            <input type="text" size="6" id="captcha" name="captcha" placeholder="Enter captcha number" value="" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <div class="form-check">
                              <input type="checkbox" id="cf-send-email" name="cf-send-email" class="form-check-input"/>
                              <label class="form-check-label" for="cf-send-email">
                                <small class="float-left">By checking this option, you are agree to recieve an email.</small>
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <input type="submit" name="cf-submitted" value="Submit" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>';
}