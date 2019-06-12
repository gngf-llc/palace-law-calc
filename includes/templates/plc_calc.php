<div id="plc_container" class="plc_calc_page">
    <h3>Learn what your workers' compensation case could be worth.</h3>
    <p>In just 3 easy steps, you can figure out how much you can receive from your worker's compensation case.</p>

	<p id="errors"></p>
	<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>#calc">
        <strong>Step 1</strong>
        <p>When did your injury occur?</p>
        <div class="timeframe_block">
            <select name="plc_month" id="month_select" required>
                <?php foreach($params['months'] as $num => $name): ?>
					<?php
					# NOTE defaulting to month 7 since thats when the
					# disability data starts and we dont want to default
					# to a month/year with no data
					?>
					<option value="<?php echo $num; ?>" <?php echo ($num == 07) ? 'selected': '' ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="plc_year" id="year_select" required>
                <?php foreach($params['years'] as $year): ?>
                    <option><?php echo $year; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <br />

        <strong>Step 2</strong>
        <p>Select your injuries from the list below. Click "Add Injury" to include multiple injuries.</p>
        <div class="injury_list">
        </div>
        <a href="#" id="add_injury">+ Add Injury</a>
        <p class="buttons step_3_toggle">
            <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Calculate My Case Worth" />
        </p>

        <div id="step_3">
	    <div class="stap-3"><strong>Step 3</strong></div>
            <p>
                <label class="description" for="plc_name">Name *</label>
                <input id="plc_last_name" name="plc_name" class="" type="text" maxlength="255" value="<?php echo (isset($_POST['plc_name']) ? esc_attr($_POST['plc_name']) : '' ); ?>" required='required' />
            </p>
            <p>
                <label class="description" for="plc_email">Email *</label>
                <input id="plc_email" name="plc_email" class="" type="email" maxlength="255" placeholder="user@example.com" value="<?php echo (isset($_POST['plc_email']) ? esc_attr($_POST['plc_email']) : '' ); ?>" required='required' />
            </p>
            <p class="buttons">
                <input type='hidden' name='plc_current_step' value='calc' id='newurl' />
                <input id="saveForm" class="button_text" type="submit" name="plc_submit" value="Show My Results" />
            </p>
        </div>

    </form>

    <?php // template that gets used in injury_list; dont style this directly ?>
    <div id="injury_block_template" style="display: none;">
        <div class='injury_select_block'>
            <label class="label_injury" for="injury"></label>
            <select name="plc_injuries[]" class="injury_select" required>
                <option value="" disabled selected>List of Injuries</option>
                <?php foreach($params['injuries'] as $injury_option): ?>
                    <?php echo $injury_option; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div class='rating_select_block'>
        </div>
        <a href="#" class="delete_injury">x</a>
    </div>

</div>
