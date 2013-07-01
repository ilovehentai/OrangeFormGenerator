<form name="form1" class="xpto" enctype="multipart/form-data" action="" method="post" id="form1">
<!--
Parcial Html FormGenerator Template
-->
<div>
    <fieldset>
    <legend class="legend_xpto">Personal information</legend>
    <div>
        <label for="name">Name</label> <input name="name" value="John Doe" maxlength="100" type="text" id="name"/><br/>
        <label for="password">Password</label> <input name="password" value="" type="password" id="password"/><br/>
        <label for="password_chk">Confirm Password</label> <input name="password" value="" type="password" id="password_chk"/><br/>
        <label for="email">Email</label> <input name="email" value="" type="text" id="email"/><br/>
        <label for="date">Birth date</label> <input name="calendario" value="0000-00-00" type="text" id="date"/><br/>
        <label for="sex">Sex</label> <label for="sex0" class="sex-label">M</label><input name="sex[]" type="radio" value="m" id="sex0"/>
<label for="sex1" class="sex-label">F</label><input name="sex[]" type="radio" value="f" id="sex1"/><br/>
    </div>
    </fieldset>
</div>
<div>
    <fieldset class="xpto">
    <legend>Extra Information</legend>
    <div>
        <label for="interess">Interess</label> <label for="interess0" class="group-label">Sports</label><input name="interesses[]" type="checkbox" value="sports" id="interess0"/>
<label for="interess1" class="group-label">Literature</label><input name="interesses[]" type="checkbox" value="l" id="interess1"/><br/>
        <label for="countrys">Countrys</label> <select name="countrys" id="countrys">
<option value="1">Portugal</option>
<option value="2">Suisse</option>
<option value="3">China</option>
<option value="4">Nippon</option>
</select><br/>
        <label for="optiongroup">Option Group</label> <select name="optiongroup" id="optiongroup">
<optgroup label="option1"><option value="1">Option 1</option>
<option value="2">Option 2</option></optgroup><optgroup label="option2"><option value="3">Option 3</option>
<option value="4">Option 4</option></optgroup>
</select><br/>
        <label for="observ">Observations</label> <textarea name="observ" id="observ">hello world
</textarea><br/>
        <label for="file">Upload ficheiro</label> <input name="file" value="" type="file" id="file"/>
    </div>
    </fieldset>
</div>
<input name="captcha" value="" type="text" id="captcha"/> 
<br class="break-float" />
<label class="small-label" for="autorizo">Autorizo</label> <input value="1" name="autorizo" type="checkbox" id="autorizo"/>
<br class="break-float" />
<ul class="list-of-buttons">
    <li><input name="hidden" value="1" type="hidden" id="hidden"/></li>
    <li><input name="button" value="button" type="button" id="button"/></li>
    <li><button name="pushbutton" value="Push Button" type="button">Push Button</button></li>
    <li><input name="reset" value="reset" type="reset" id="reset"/></li>
    <li><input name="image" value="image" type="image" src="/images/upload_64.png" id="image"/></li>
    <li><input name="submit" value="submit" type="submit" id="submit"/></li>
</ul>

</form>
<script>
    $(function() {

        options = {
                    fields: {"name" : {validator : "not_empty", msg : "The name field is mandatory"},
"password" : {validator : "regExpression:/^[a-zA-Z0-9]{8}$/", msg : "The password field is mandatory and must contain 8 alphanumerics caracters"},
"email" : {validator : "email", msg : "The email seems invalid"},
"sex" : {validator : "checked", msg : "The sex field is mandatory"},
"countrys" : {validator : "selected", msg : "The country is mandatory"}},
                    title_msg: "Error in fields form",
                    error_msg: "Some errors were founds"
                  };
        $("#form1").validate(options);

    });
</script>