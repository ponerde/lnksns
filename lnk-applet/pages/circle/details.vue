<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box"
			:style="{paddingTop: statusBarHeight+'px',background: 'rgba(255, 255, 255, '+navbarTrans+')'}">
			<view class="nav-item df">
				<view class="nav-back df" @click="navBack">
					<image :src="'/static/img/'+(navbarTrans==1?'back.png':'backw.png')"></image>
				</view>
				<view v-if="scrollTop>350" class="nav-name">{{ info.name }}</view>
			</view>
		</view>
		<!-- User Info -->
		<view class="user-info">
			<image class="avatar-box" :src="info.avatar" mode="aspectFill"></image>
			<view class="name-box df">{{ info.name }}</view>
			<view class="cu-img-group" v-if="info.user_count"
				:data-url="'circle/fans?id='+info.id+'&name='+info.name+'&user_count='+info.user_count"
				@click="navigateToFun">
				<view class="cu-img" v-for="(img,i) in info.user" :key="i">
					<image :src="img" mode="aspectFill"></image>
				</view>
				<text>{{ info.user_count }} 位圈友</text>
			</view>
			<view class="introduce">{{ info.intro }}</view>
			<view class="user-btn bg1" :class="{'bg2':info.is_follow}" @click="followCircle">
				{{ info.is_follow? '已加入':'加入 '+info.name }}
			</view>
		</view>
		<!-- Bar Box -->
		<view class="bar-box df" :style="{top:statusBarHeight+43+'px'}">
			<block v-for="(item,idx) in bararr" :key="idx">
				<view v-if="idx>0" class="bar-line"></view>
				<view @click="barClick" :data-i="idx" class="bar-item" :class="{'bar-active':idx==baridx}">
					{{item}}
				</view>
			</block>
		</view>
		<!-- Content Box -->
		<view class="content">
			<view v-if="isEmpty" class="empty-box">
				<image :src="insetUrl+'4.png'" />
				<view class="text">空..</view>
			</view>
			<view class="content-box">
				<block v-for="(item,idx) in list" :key="item.id">
					<dynamicBox :item="item" :idx="idx" @likeback="likeclick"></dynamicBox>
				</block>
			</view>
			<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
			<view v-else class="no-more">没有更多了️</view>
		</view>
		<!-- Tips Box -->
		<uni-popup ref="tipsPopup" type="top" mask-background-color="rgba(0, 0, 0, 0)">
			<view class="tips-box df" :style="{marginTop: statusBarHeight+44+'px'}">
				<view class="tips-item">{{tips_title}}</view>
			</view>
		</uni-popup>
	</view>
</template>

<script>
	const app = getApp();
	const api = require('@/config/api');
	const util = require('@/utils/util');
	import dynamicBox from '@/components/dynamic/box.vue';
	export default {
		components: {
			dynamicBox
		},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				insetUrl: api.insetUrl,
				scrollTop: 0,
				navbarTrans: 0,
				info: {
					name: '...',
					intro: '...',
					user_count: 0,
				},
				bararr: ['推荐', '最新'],
				baridx: 0,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
				tips_title:'',
			}
		},
		async onLoad(option) {
			uni.showShareMenu();
			await this.$onLaunched;
			let that = this;
			if (option.id && option.id > 0) {
				that.id = option.id;
				that.getCircleDetails();
				that.getCircleDynamic();
			} else {
				that.tips_title = '未找到圈子或圈子异常！';
				that.$refs.tipsPopup.open();
				setTimeout(function() {
					that.$refs.tipsPopup.close();
					that.navBack();
				}, 2000);
			}
		},
		methods: {
			getCircleDetails() {
				let that = this;
				util.request(api.getCircleDetailsUrl, {
					id: that.id
				}).then(function(res) {
					if (res.code == 200) {
						that.info = res.data;
					} else {
						that.tips_title = res.msg;
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
							that.navBack();
						}, 2000);
					}
				});
			},
			getCircleDynamic() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.getCircleDynamicUrl, {
					id: that.id,
					type: that.baridx,
					page: that.page
				}).then(function(res) {
					that.isFun = true;
					if (res.data && res.data.data.length) {
						that.list = that.list.concat(res.data.data);
						that.page = res.data.current_page;
						that.loadStatus = 'more';
					} else if (that.page == 1) {
						that.isEmpty = true;
						that.loadStatus = 'more';
					} else {
						that.loadStatus = 'no-more';
					}
				});
			},
			followCircle() {
				let that = this;
				util.request(api.followCircleUrl, {
					id: that.id,
					is_follow: that.info.is_follow,
				}, 'POST').then(function(res) {
					that.info.is_follow = !that.info.is_follow;
				});
			},
			barClick(e) {
				if (this.isFun) {
					this.baridx = e.currentTarget.dataset.i;
					this.isFun = false;
					this.list = [];
					this.page = 1;
					this.getCircleDynamic();
				}
			},
			likeclick(e) {
				this.list[e.idx].is_like = e.is_like;
				this.list[e.idx].like_count = e.like_count;
			},
			navigateToFun(e) {
				let url = e.currentTarget.dataset.url;
				uni.navigateTo({
					url: '/pages/' + url
				})
			},
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/circle'
					})
				}
			},
		},
		onReachBottom() {
			if (this.list.length) {
				this.page = this.page + 1;
				this.getCircleDynamic();
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
			let frontColor = "#ffffff";
			var trans = (e.scrollTop > 350 ? 350 : e.scrollTop) / 350;
			if (trans >= 1) {
				frontColor = "#000000";
			}
			this.navbarTrans = trans;
			uni.setNavigationBarColor({
				frontColor: frontColor,
				backgroundColor: "#ffffff",
				animation: {
					duration: 400,
					timingFunc: 'easeIn'
				}
			})
		},
		onShareAppMessage: function() {},
		onShareTimeline(res) {
			return {
				title: app.globalData.shareTitle
			}
		},
	}
</script>

<style>
	@import url("/static/css/circle/details.css");
</style>