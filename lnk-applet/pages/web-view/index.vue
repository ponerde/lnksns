<template>
	<view>
		<web-view :webview-styles="webviewStyles" :src="url"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				url: '',
				webviewStyles: {
					progress: {
						color: '#000000'
					}
				}
			};
		},
		methods: {},
		onLoad(options) {
			this.url = decodeURIComponent(options.url);
		},
	}
</script>

<style>
</style>
