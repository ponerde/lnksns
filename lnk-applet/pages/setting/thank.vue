<template>
	<view class="container">
		<!-- Nav Bar -->
		<view class="nav-bar"
			:style="{paddingTop: statusBarHeight+'px',background: 'rgba(255, 255, 255, '+navbarTrans+')'}">
			<view class="blck-box df" @click="navBack">
				<image src="/static/img/back.png"></image>
			</view>
		</view>
		<!-- Box -->
		<view class="bg-box">
			<image class="bg-img" :src="insetUrl+'2.png'" mode="aspectFill" />
		</view>
		<view class="box">
			<view class="box-title">{{ appName }}要特别感谢这些重要的朋友和团队 👏</view>
			<view class="box-doc">以下排列不分先后顺序</view>
			<view class="data-box">
				<view class="data-item df" v-for="(v,i) in list" :key="i">
					<image v-if='v.img' :src="v.img" mode="aspectFill"></image>
					<image v-else src="/static/img/1.png"></image>
					<view class="data-txt">{{v.name}}</view>
				</view>
			</view>
		</view>
		<view class="box-doc btext">
			<view>{{ appTitle }}</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		components: {},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				insetUrl: api.insetUrl,
				appName: api.appName,
				appTitle: api.appTitle,
				navbarTrans: 0,
				list: []
			}
		},
		onLoad(options) {},
		methods: {
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/index'
					})
				}
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
			var trans = (e.scrollTop > 88 ? 88 : e.scrollTop) / 88;
			this.navbarTrans = trans;
		},
	}
</script>

<style>
	@import url("/static/css/setting/thank.css");
</style>