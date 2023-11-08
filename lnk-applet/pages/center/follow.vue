<template>
	<view class="container">
		<view class="content">
			<view v-if="isEmpty" class="empty-box">
				<image :src="insetUrl+'10.png'" />
				<view class="text">空..</view>
			</view>
			<block v-for="(v,i) in list" :key="i">
				<view class="user-list df" :data-id="v.user.id" @click="toUser">
					<view class="user-list-info df">
						<image class="avatar" :src="v.user.avatar"></image>
						<view class="name df">
							<view class="ohto">{{v.user.name}}</view>
						</view>
					</view>
					<view class="user-list-btn bg1" :data-i="i" :data-is_follow="v.is_follow" @click.stop="followUser"
						:class="{'bg2':type==2 && !v.is_follow}">
						{{v.is_follow?'相互关注':type==1?'已关注':'回粉'}}
					</view>
				</view>
			</block>
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
	export default {
		components: {},
		data() {
			return {
				statusBarHeight: app.globalData.statusBarHeight,
				insetUrl: api.insetUrl,
				title: '...',
				type: 1,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
				tips_title: '',
			}
		},
		onLoad(options) {
			if (options.type) {
				this.title = '你关注的';
				if (options.type == 2) {
					this.title = '关注你的';
				}
				this.type = options.type;
			}
			uni.setNavigationBarTitle({
				title: this.title
			});
			this.userFollow();
		},
		methods: {
			userFollow() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.userFollowUrl, {
					type: that.type,
					page: that.page,
				}).then(function(res) {
					that.isFun = true;
					if (res.data.data.length > 0) {
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
			followUser(e) {
				let that = this;
				let i = e.currentTarget.dataset.i;
				let is_follow = e.currentTarget.dataset.is_follow;
				util.request(api.followUserUrl, {
					uname: uni.getStorageSync('user_info').name,
					user_id: that.list[i].user.id,
					is_follow: that.type == 1 ? true : is_follow
				}, 'POST').then(function(res) {
					if (res.code == 200) {
						if (that.type == 1) {
							that.list.splice(i, 1);
							if (!that.list.length) {
								that.isEmpty = true;
								that.loadStatus = 'more';
							}
						} else {
							that.list[i].is_follow = !that.list[i].is_follow;
						}
					} else {
						that.tips_title = res.msg;
						that.$refs.tipsPopup.open();
						setTimeout(function() {
							that.$refs.tipsPopup.close();
						}, 1500);
					}
				});
			},
			toUser(e) {
				uni.navigateTo({
					url: '/pages/user/details?id=' + e.currentTarget.dataset.id
				})
			},
			navBack() {
				let currentRoutes = getCurrentPages();
				if (currentRoutes.length > 1) {
					uni.navigateBack();
				} else {
					uni.switchTab({
						url: '/pages/tabbar/center'
					})
				}
			}
		},
	}
</script>

<style>
	@import url("/static/css/center/follow.css");
</style>