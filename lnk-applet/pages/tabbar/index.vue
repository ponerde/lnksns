<template>
	<view class="container">
		<!-- Nav Box -->
		<view class="nav-box" :style="{paddingTop: statusBarHeight+'px'}">
			<view class="nav-item df">
				<view class="title-label df" @click="typeClick" :data-i="i" v-for="(item,i) in typearr" :key="i">
					<text
						:style="{color:typeidx==i?'#000':'#999',fontSize:typeidx==i && scrollTop<88 ?'36rpx':'26rpx'}">
						{{item}}
					</text>
					<view v-if="typeidx==i" class="active"></view>
				</view>
			</view>
		</view>
		<!-- Content Box -->
		<view class="content-box" :style="{marginTop: statusBarHeight+44+'px'}">
			<view class="empty-box" v-if="isEmpty">
				<image v-if="typeidx==0" :src="insetUrl+'5.png'" mode="aspectFill" />
				<image v-else :src="insetUrl+'4.png'" mode="aspectFill" />
				<view v-if="typeidx==0" class="text">没有关注的好朋友..</view>
				<view v-else class="text df">
					<view>没有内容，</view>
					<view data-url="dynamic/add" @click="navigateToFun" style="color: #4cd964;">去发一个</view>
				</view>
			</view>
			<block v-else v-for="(item,idx) in list" :key="item.id">
				<dynamicBox :item="item" :idx="idx" @likeback="likeclick"></dynamicBox>
			</block>
		</view>
		<uni-load-more v-if="loadStatus != 'no-more'" :status="loadStatus"></uni-load-more>
		<view v-else class="no-more">没有更多了️</view>
		<!-- Add Box -->
		<view class="add-box" data-url="dynamic/add" @click="navigateToFun">＋</view>
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
				typearr: ['关注', '推荐'],
				typeidx: 1,
				list: [],
				page: 1,
				isEmpty: false,
				isFun: true,
				loadStatus: 'more',
			}
		},
		onPullDownRefresh() {
			this.list = [];
			this.page = 1;
			this.recommendDynamic();
			uni.stopPullDownRefresh();
		},
		async onLoad() {
			uni.showShareMenu();
			await this.$onLaunched;
			this.recommendDynamic();
		},
		onShow() {
			this.getMessageCount();
		},
		methods: {
			recommendDynamic() {
				let that = this;
				that.loadStatus = 'loading';
				that.isEmpty = false;
				util.request(api.recommendDynamicUrl, {
					type: that.typeidx,
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
			typeClick(e) {
				if (this.isFun) {
					this.typeidx = e.currentTarget.dataset.i;
					this.isFun = false;
					this.list = [];
					this.page = 1;
					this.recommendDynamic();
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
			getMessageCount() {
				util.request(api.messageCountUrl).then(function(res) {
					if (res.data) {
						uni.setTabBarBadge({
							index: 2,
							text: res.data.toString()
						})
					} else {
						uni.removeTabBarBadge({
							index: 2
						})
					}
				})
			}
		},
		onReachBottom() {
			if (this.list.length) {
				this.page = this.page + 1;
				this.recommendDynamic();
			}
		},
		onPageScroll(e) {
			this.scrollTop = e.scrollTop;
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
	@import url("/static/css/tabbar/index.css");
</style>