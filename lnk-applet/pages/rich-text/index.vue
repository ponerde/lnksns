<template>
	<view>
		<view style="width: calc(100%-60rpx);margin: 30rpx 30rpx 0;">
			<mp-html :content="htmlSnip"></mp-html>
		</view>
	</view>
</template>

<script>
	import mpHtml from '@/components/mp-html/mp-html';
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		components: {
			mpHtml
		},
		data() {
			return {
				htmlSnip: '',
				type: 0,
				id: 0,
			};
		},
		onLoad(options) {
			this.id = options.id;
			this.getClause();
		},
		methods: {
			getClause() {
				let that = this;
				util.request(api.clauseUrl, {
					id: that.id
				}).then(function(res) {
					uni.setNavigationBarTitle({
						title: res.data.title
					})
					that.htmlSnip = res.data.content;
				})
			},
		},
	}
</script>

<style>
	page{
		background: white;
	}
</style>
