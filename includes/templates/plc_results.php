<div id="">
    <strong>Based on your input, your case may be worth $<?php echo $params['value']; ?></strong>
    <p>It's important to note that your case's value is highly depended on many subjective factors. An experienced workers compensation attorney like those at Palace Law can help you maximize the benefits that are owed to you.</p>
    <p>Complete the contact form to the right if you would like a Palce Law attorney to evaluate your results and provide you a more accurate assessment of what they think your case can be worth.</p>
</div>

<div id="plc_form_container">
    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <p>
            <label class="description" for="plc_first_name">First Name *</label>
            <input id="plc_first_name" name="plc_first_name" class="" type="text" maxlength="255" value="<?php echo (isset($_POST['plc_first_name']) ? esc_attr($_POST['plc_first_name']) : '' ); ?>" required='required' />
        </p>
        <p>
            <label class="description" for="plc_last_name">Last Name *</label>
            <input id="plc_last_name" name="plc_last_name" class="" type="text" maxlength="255" value="<?php echo (isset($_POST['plc_last_name']) ? esc_attr($_POST['plc_last_name']) : '' ); ?>" required='required' />
        </p>
        <p>
            <label class="description" for="plc_email">Email *</label>
            <input id="plc_email" name="plc_email" class="" type="email" maxlength="255" placeholder="user@example.com" value="<?php echo (isset($_POST['plc_email']) ? esc_attr($_POST['plc_email']) : '' ); ?>" required='required' />
        </p>
        <p>
            <label class="description" for="plc_phone">Phone </label>
            <input id="plc_phone" name="plc_phone" class="" type="text" maxlength="24" placeholder="(###) ###-####" value="<?php echo (isset($_POST['plc_phone']) ? esc_attr($_POST['plc_phone']) : '' ); ?>" />
        </p>
        <p>
            <label class="description" for="plc_message">Comments</label>
            <textarea id="plc_message" name="plc_message" class=""><?php echo (isset($_POST['plc_message']) ? esc_attr($_POST['plc_message']) : '' ); ?></textarea>
        </p>

        <p class="buttons">
            <input type='hidden' name='plc_current_step' value='results' />
            <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Submit" />
        </p>
    </form>
</div>
