<?php if ($variation1): ?>
	<label class="variationlabel" for="variation1"><?php echo $this->site->config['shopVariation1']; ?>:</label>
	<select name="variation1" class="variation" id="variation1">
		<?php foreach ($variation1 as $variation): ?>
			<option value="<?php echo $variation['variationID']; ?>"><?php echo $variation['variation']; ?>
			<?php if ($variation['price'] > 0) echo '+'.currency_symbol().$variation['price']; ?>
			<?php if ($variation['backorder']==1) echo "(Backorder)"; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<br class="clear" />
<?php endif; ?>

<?php if ($variation2): ?>
	<label class="variationlabel" for="variation2"><?php echo $this->site->config['shopVariation2']; ?>:</label>
	<select name="variation2" class="variation" id="variation2">
		<?php foreach ($variation2 as $variation): ?>
			<option value="<?php echo $variation['variationID']; ?>"><?php echo $variation['variation']; ?>
			<?php if ($variation['price'] > 0) echo '+'.currency_symbol().$variation['price']; ?>
			<?php if ($variation['backorder']==1) echo "(Backorder)"; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<br class="clear" />
<?php endif; ?>

<?php if ($variation3): ?>
	<label class="variationlabel" for="variation3"><?php echo $this->site->config['shopVariation3']; ?>:</label>
	<select name="variation3" class="variation" id="variation3">
		<?php foreach ($variation3 as $variation): ?>
			<option value="<?php echo $variation['variationID']; ?>"><?php echo $variation['variation']; ?>
			<?php if ($variation['price'] > 0) echo '+'.currency_symbol().$variation['price']; ?>
			<?php if ($variation['backorder']==1) echo "(Backorder)"; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<br class="clear" />
<?php endif; ?>