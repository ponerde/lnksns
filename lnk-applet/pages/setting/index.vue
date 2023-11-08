<template>
	<view class="container df">
		<view class="table">账号</view>
		<view class="list-box">
			<button class="list df" data-url="center/means" @click="navigateToFun">
				<image class="icon" src="/static/img/setting/1.png"></image>
				<view class="list-item df">
					<view class="title">个人资料修改</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
		</view>
		<view class="table">帮助</view>
		<view class="list-box">
			<button class="list df" open-type="contact">
				<image class="icon" src="/static/img/setting/3.png"></image>
				<view class="list-item df">
					<view class="title">在线客服</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
		</view>
		<view class="table">关于</view>
		<view class="list-box">
			<button class="list df" @click="toRichText(3)">
				<image class="icon" src="/static/img/setting/6.png"></image>
				<view class="list-item df bb1">
					<view class="title">隐私政策</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
			<button class="list df" @click="toRichText(2)">
				<image class="icon" src="/static/img/setting/7.png"></image>
				<view class="list-item df bb1">
					<view class="title">用户协议</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
			<button class="list df" @click="toGzh" v-if="config.lnk_gzh">
				<image class="icon" src="/static/img/setting/8.png"></image>
				<view class="list-item df bb1">
					<view class="title">关注我们</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
			<button class="list df" @click="toGw" v-if="config.lnk_gw">
				<image class="icon" src="/static/img/setting/9.png"></image>
				<view class="list-item df">
					<view class="title">访问官网</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
			<button class="list df" data-url="setting/thank" @click="navigateToFun">
				<image class="icon" src="/static/img/setting/11.png"></image>
				<view class="list-item df">
					<view class="title">特别鸣谢</view>
					<image src="/static/img/back.png"></image>
				</view>
			</button>
		</view>
		<view class="out_btn" @click="shutLogin">清除缓存</view>
		<view class="version df">
			<text style="margin-bottom: 10rpx;">{{ appName }} Version 1.0</text>
			<text>{{ appCopyrightCn }}</text>
			<text>{{ appCopyrightEn }}</text>
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
				appName: api.appName,
				appCopyrightCn: api.appCopyrightCn,
				appCopyrightEn: api.appCopyrightEn,
				config: {},
			}
		},
		onLoad() {
			this.getConfig();
		},
		methods: {
			getConfig() {
				let that = this;
				util.request(api.configUrl).then(function(res) {
					that.config = res.data;
				})
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			toGzh() {
				if (this.config.lnk_gzh) {
					var url = encodeURIComponent(this.config.lnk_gzh);
					uni.navigateTo({
						url: '/pages/web-view/index?url=' + url
					})
				}
			},
			toGw() {
				if (this.config.lnk_gw) {
					uni.navigateTo({
						url: '/pages/web-view/index?url=' + this.config.lnk_gw
					})
				}
			},
			toRichText(id) {
				uni.navigateTo({
					url: '/pages/rich-text/index?id=' + id
				})
			},
			shutLogin() {
				uni.showModal({
					title: '确定要退出登录吗？',
					success: function(res) {
						if (res.confirm) {
							uni.clearStorageSync();
							uni.reLaunch({
								url: '/pages/tabbar/index',
							})
						}
					}
				});
			}
		},
	}
</script>

<style>
	@import url("/static/css/setting/index.css");
</style>