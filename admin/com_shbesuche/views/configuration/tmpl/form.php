<?php 

defined('_JEXEC') or die('Restricted access');



?>


<div class="bootstrap-wrap">
 
<form action="index.php" method="POST" name="adminForm" id="adminForm" class="form-horizontal">
<div class="row-fluid">


	<div class="span12">

		<ul class="nav nav-tabs">
			<li class="active"><a href="#messages-setup" data-toggle="tab"><?php echo Jtext::_('COM_SHBESUCHE_CONFIG_MESSAGES_SETUP');?></a></li>
			<li class=""><a href="#view-settings" data-toggle="tab"><?php echo JText::_('COM_SHBESUCHE_CONFIG_VIEW_SETTINGS');?></a></li>
			<li class=""><a href="#date-configuration" data-toggle="tab"><?php echo JText::_('COM_SHBESUCHE_CONFIG_DATE_CONFIGURATION_HEADING');?></a></li>
			<li class=""><a href="#publish-subscribe-settings" data-toggle="tab"><?php echo JText::_('COM_SHBESUCHE_CONFIG_SUBSCRIBE_DETAILS');?></a></li>
			<li class=""><a href="#debug-settings" data-toggle="tab"><?php echo JText::_('COM_SHBESUCHE_CONFIG_DEBUG_SETTINGS');?></a></li>
		</ul>
	<div class="tab-content">

	<div class="tab-pane active" id="messages-setup">
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_EMAIL_SUBJECT' ); ?>:</label>
			<div class="controls">
				<input class="input-xxlarge" type="text" name="email_subject" id="email_subject" value="<?php echo $this->configuration['email_subject'];?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_EMAIL_BODY' ); ?>:</label>
			<div class="controls">
				<textarea class="input-xxlarge" rows="10" name="email_body" id="email_body"/><?php echo $this->configuration['email_body'];?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_CALENDAR_MSG' ); ?>:</label>
			<div class="controls">
				<textarea style="" class="input-xxlarge" rows="10" name="calendar_message" id="calendar_message"/><?php echo $this->configuration['calendar_message'];?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_CREATE_MSG' ); ?>:</label>
			<div class="controls">
				<textarea class="input-xxlarge" rows="10" name="create_message" id="calendar_message"/><?php echo $this->configuration['create_message'];?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_BCC_ADMIN' ); ?>:</label>
			<div class="controls">
				<input type="checkbox" name="bcc_admin" value=1 <?php echo (isset($this->configuration['bcc_admin']) && $this->configuration['bcc_admin'] == 1) ? 'checked="checked"' : '';?>/>
			</div>
		</div>
	</div>
		


	<div class="tab-pane" id="view-settings">
		
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_SHOW_LINK' ); ?>:</label>
			<div class="controls">
				<input type="checkbox" name="show_link" value="1" <?php if ($this->configuration['show_link']) {echo "checked=\"checked\"";}?>/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_SHOW_PRICES' ); ?>:</label>
			<div class="controls">
				<input type="checkbox" name="show_prices" value="1" <?php if ($this->configuration['show_prices']) {echo "checked=\"checked\"";}?>/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_TIMEINCREMENT' ); ?>:</label>
			<div class="controls">
				<input type="text"  class="input-mini" name="time_increment" value="<?php echo (isset($this->configuration['time_increment']) ? $this->configuration['time_increment'] : '30');?>"/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_CONFIG_START_DAY' ); ?>:</label>
			<div class="controls">
				<?php $bow = date_create('last Sunday',new DateTimeZone(SHBESUCHE_TIMEZONE));?>
            	<?php $bow->setTimezone(new DateTimeZone(SHBESUCHE_TIMEZONE));?>
				<select name="calendar_start_day">
            		<?php for ($i=0;$i<=6;$i++) :?>
            			<option value="<?php echo $bow->format('w');?>" <?php echo ($this->configuration['calendar_start_day'] == $bow->format('w')) ? 'selected = true' : null;?> >
            				<?php echo Jhtml::_('date',$bow->format(DATE_ATOM),'l');?>
            			</option>
            			<?php $bow->modify('+1 day');?>
            		<?php endfor;?>
            	</select>
            </div>
        </div>

        <div class="control-group">
			<label class="control-label"><?php echo Jtext::_('COM_SHBESUCHE_CONFIG_FIELDS_IN_MANAGE_DIARIES');?></label>
			<div class="controls">
        		<?php foreach ($this->customfields as $field) :?>
        			<label class="checkbox">
        				<input type="checkbox" name="fields[]" value="<?php echo $field->varname;?>" <?php echo ($this->configuration['manage_fields'] != '' && in_array($field->varname,json_decode($this->configuration['manage_fields']))) ? 'checked' : null;?>/>
						<?php echo $field->fieldname;?>
					</label>
				<?php endforeach;?>
				<label class="checkbox">
					<input type="checkbox" name="fields[]" value="_service_" <?php echo ($this->configuration['manage_fields'] != '' && in_array('_service_',json_decode($this->configuration['manage_fields']))) ? 'checked' : null;?> />
					<?php echo JText::_('COM_SHBESUCHE_CONFIG_FIELD_SERVICE');?>
				</label>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo Jtext::_('COM_SHBESUCHE_CONFIG_CURRENCY_SYMBOL_POSITON');?></label>
			<div class="controls">
				<select name="currency_symbol_before">
        			<option value="1" <?php echo ($this->configuration['currency_symbol_before'] == 1) ? 'selected=true' : null;?> ><?php echo JText::_('COM_SHBESUCHE_CONFIG_CURRENCY_SYMBOL_BEFORE');?></option>
        			<option value="0" <?php echo ($this->configuration['currency_symbol_before'] == 0) ? 'selected=true' : null;?> ><?php echo JText::_('COM_SHBESUCHE_CONFIG_CURRENCY_SYMBOL_AFTER');?></option>
        		</select>
        	</div>
        </div>

	</div>




	<div class="tab-pane" id="date-configuration">
		<p>For compatibility with PBBooking please ensure all date formats are in the format accepted by PHP's date command.</p>
		<p>Refer to the <?php echo JHtml::_('link','http://www.php.net/manual/en/function.date.php','PHP reference manual');?> for an example.</p>
		<div class="control-group">
			<label class="control-label">Calendar Heading Date Format</label>
			<div class="controls">
				<input type="text" class="input-medium" name="date_format_heading" id="date_format_heading" size="64" maxlength="250" value="<?php echo $this->configuration['date_format_heading'];?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Calendar Cell Date Format</label>
			<div class="controls">
				<input type="text" class="input-medium" name="date_format_cell" id="date_format_cell" size="64" maxlength="250" value="<?php echo $this->configuration['date_format_cell'];?>" />
	        </div>
	    </div>

	    <div class="control-group">
	    	<label class="control-label">Confirmation Messages Date Format:</label>
	    	<div class="controls">
				<input type="text" class="input-medium" name="date_format_message" id="date_format_message" size="64" maxlength="250" value="<?php echo $this->configuration['date_format_message'];?>" />
	        </div>
	     </div>
	</div>

	<div class="tab-pane" id="publish-subscribe-settings">
		<p>Please be careful configuring this.  This section will allow remote downloads of all details stored in your calendars.</p>
		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_ALLOW_SUBSCRIBE' ); ?>:</label>
			<div class="controls">
				<input type="checkbox" name="allow_subscribe" id="allow_subscribe" value = "1"<?php if ($this->configuration['allow_subscribe']):?> checked<?php endif;?>/>
	        </div>
	    </div>

	    <div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_SUBSCRIBE_SECRET' ); ?>:</label>
			<div class="controls">
				<input type="text" class="input-large" name="subscribe_secret" id="subscribe_secret" value="<?php echo $this->configuration['subscribe_secret'];?>"/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_ALLOW_PUBLISHING' ); ?>:</label>
			<div class="controls">
				<input type="checkbox" name="allow_publish" id="allow_publish" value = "1"<?php if ($this->configuration['allow_publish']):?> checked<?php endif;?>/>
	        </div>
	   	</div>

	   	<div class="control-group">
	   		<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_PUBLISHING_USE_OWN_SECURITY' ); ?>:</label>
	   		<div class="controls">
	   			<input type="checkbox" name="use_pb_pub_sec" id="use_pb_pub_sec" value = "1"<?php if ($this->configuration['use_pb_pub_sec']):?> checked<?php endif;?>/>
	   		</div>
	   	</div>

	   	<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_PUBLISH_USERNAME' ); ?>:</label>
			<div class="controls">
				<input type="text" name="publish_username"  class="input-medium" id="publish_username" value="<?php echo $this->configuration['publish_username'];?>"/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_SHBESUCHE_PUBLISH_PASSWORD' ); ?>:</label>
			<div class="controls">
				<input type="text" name="publish_password" class="input-medium" id="publish_password" value="<?php echo $this->configuration['publish_password'];?>"/>
			</div>
		</div>
	</div>

	<div class="tab-pane" id="debug-settings">
		<div class="control-group">
			<label class="control-label"><?php echo JText::_('COM_SHBESUCHE_ENABLE_LOGGING');?></label>
			<div class="controls">
				<input type="checkbox" name="enable_logging" value="1" <?php echo (isset($this->configuration['enable_logging']) && $this->configuration['enable_logging'] == 1) ? 'checked' : null;?>>
			</div>
		</div>
	</div>

	

</div>
</div>



</div>
 
<div class="clr"></div>
 
<input type="hidden" name="option" value="com_shbesuche" />
<input type="hidden" name="id" value="<?php echo $this->configuration['id']; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="configuration" />
</form>

</div>