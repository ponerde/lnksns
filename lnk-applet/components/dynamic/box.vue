<template>
	<view>
		<view class="card-item" @click="toDynamic">
			<view class="card-item-avatar" @click.stop="toUser">
				<image :src="item.user.avatar" mode="aspectFill"></image>
			</view>
			<view class="card-item-info">
				<view class="item-info-user" @click.stop="toUser">
					<view class="info-user-name ohto">{{item.user.name}}</view>
					<view class="info-user-tag" v-if="item.user.career">@{{item.user.career}}</view>
				</view>
				<view class="item-info-file" v-if="item.img_count">
					<block v-if="item.img_count == 1">
						<view class="info-wh"
							:class="{'info-h-w':item.img[0].wide < item.img[0].high,'info-w-h':item.img[0].wide > item.img[0].high}">
							<lazy-load :src="item.img[0].url" border-radius="8rpx"></lazy-load>
						</view>
					</block>
					<view v-else class="info-img-box" v-for="(img,i) in item.img" :key="i">
						<lazy-load :src="img.url" border-radius="8rpx"></lazy-load>
					</view>
					<view class="img-count df" v-if="item.img_count>2">
						<image src="/static/img/i.png"></image>
						<text>{{item.img_count}}</text>
					</view>
				</view>
				<view class="item-info-content ohto2">{{item.content}}</view>
				<view class="df">
					<view class="info-topic df" @click.stop="toCircle">
						<image src="/static/img/qz.png"></image>
						<view class="name">{{item.circle_name}}</view>
					</view>
					<view v-if="item.adds" class="info-topic df" @click.stop="openLocationClick">
						<image src="/static/img/dw.png"></image>
						<view class="name ohto">{{item.adds}}</view>
					</view>
				</view>
				<view class="item-info-quantity df">
					<text class="item-info-date">{{item.create_time}}</text>
					<view class="df">
						<image src="/static/img/pl.png"></image>
						<text>{{item.comment_count?item.comment_count:'评论'}}</text>
						<image @click.stop="likeClick" v-if="item.is_like" src="/static/img/xhs.png"></image>
						<image @click.stop="likeClick" v-else src="/static/img/xh.png"></image>
						<text>{{item.like_count?item.like_count:'赞'}}</text>
					</view>
				</view>
				<view class="info-comment ohto" v-if="item.comment">
					<text class="nm">{{item.comment.user_name}}：</text><text>{{item.comment.content}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	const api = require('@/config/api');
	const util = require('@/utils/util');
	export default {
		props: {
			item: {
				type: Object,
				require: true
			},
			idx: {
				type: Number,
				default: 0
			},
		},
		data() {
			return {}
		},
		methods: {
			likeClick() {
				let that = this;
				let is_like = !that.item.is_like;
				let like_count = that.item.like_count;
				if (is_like) like_count++;
				if (!is_like) like_count--;
				this.$emit('likeback', {
					idx: that.idx,
					is_like: is_like,
					like_count: like_count
				})
				util.request(api.likeDynamicUrl, {
					id: that.item.id,
					duid: that.item.user_id,
					is_like: is_like,
					content: that.item.content,
					img: that.item.type == 1 ? that.item.img[0].url : that.item.type == 2 ? that.item.video.img :
						'',
				}, 'POST');
			},
			toDynamic() {
				uni.navigateTo({
					url: '/pages/dynamic/details?id=' + this.item.id
				})
			},
			toUser() {
				uni.navigateTo({
					url: '/pages/user/details?id=' + this.item.user_id
				})
			},
			toCircle() {
				uni.navigateTo({
					url: '/pages/circle/details?id=' + this.item.circle_id
				})
			},
			openLocationClick(e) {
				uni.openLocation({
					latitude: parseFloat(this.item.lat),
					longitude: parseFloat(this.item.lng),
					name: this.item.adds
				});
			},
		}
	}
</script>

<style lang="scss" scoped>
	.card-item {
		width: 100%;
		background-color: #ffffff;
		display: flex;
		justify-content: space-between;
		padding-bottom: 40rpx;
	}

	.card-item .card-item-avatar {
		width: 68rpx;
		height: 68rpx;
		border-radius: 50%;
		overflow: hidden;
	}

	.card-item .card-item-avatar image {
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}

	.card-item .card-item-info {
		width: calc(100% - 88rpx);
	}

	.card-item-info .item-info-user {
		width: 100%;
		height: 68rpx;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.item-info-user .info-user-name {
		font-size: 26rpx;
		font-weight: bold;
	}

	.item-info-user .info-user-tag {
		color: #999;
		font-size: 20rpx;
		font-weight: 500;
	}

	.card-item-info .item-info-content {
		margin-top: 24rpx;
		color: #333;
		font-size: 28rpx;
		font-weight: 500;
		white-space: pre-line;
	}

	.card-item-info .item-info-file {
		margin-top: 24rpx;
		display: flex;
		position: relative;
	}

	.card-item-info .info-wh {
		width: 375rpx;
		height: 375rpx;
	}

	.card-item-info .info-h-w {
		width: 350rpx;
		height: 450rpx;
	}

	.card-item-info .info-w-h {
		width: 500rpx;
		height: 350rpx;
	}

	.item-info-file .info-img-box {
		width: 200rpx;
		height: 200rpx;
		margin-right: 8rpx;
	}

	.info-img-box:nth-child(3n) {
		margin-right: 0rpx !important;
	}

	.item-info-file .img-count {
		position: absolute;
		right: 20rpx;
		bottom: 40rpx;
		justify-content: center;
		width: 60rpx;
		height: 40rpx;
		color: #fff;
		font-size: 20rpx;
		font-weight: bold;
		background: rgba(0, 0, 0, .5);
		border-radius: 8rpx;
	}

	.item-info-file .img-count image {
		margin-right: 6rpx;
		width: 24rpx;
		height: 24rpx;
	}

	.card-item-info .info-topic {
		margin: 24rpx 15rpx 0 0;
		padding: 0 20rpx 0 15rpx;
		height: 60rpx;
		border-radius: 30rpx;
		border: 2rpx solid #f5f5f5;
	}

	.card-item-info .info-topic image {
		width: 32rpx;
		height: 32rpx;
		border-radius: 50%;
	}

	.card-item-info .info-topic .name {
		max-width: 320rpx;
		margin-left: 8rpx;
		color: #333;
		font-size: 24rpx;
		line-height: 32rpx;
	}

	.card-item-info .item-info-quantity {
		margin-top: 24rpx;
		width: 100%;
		justify-content: space-between;
	}

	.card-item-info .item-info-quantity .item-info-date {
		color: #999;
		font-size: 24rpx;
		font-weight: 400;
	}

	.card-item-info .item-info-quantity image {
		margin-left: 30rpx;
		width: 48rpx;
		height: 48rpx;
	}

	.card-item-info .item-info-quantity text {
		margin-left: 8rpx;
		font-size: 22rpx;
		font-weight: bold;
	}

	.card-item-info .info-comment {
		margin-top: 24rpx;
		width: calc(100% - 40rpx);
		padding: 25rpx 20rpx;
		background: #f8f8f8;
		border-radius: 8rpx;
		color: #333;
		line-height: 26rpx;
		font-size: 26rpx;
	}

	.card-item-info .info-comment .nm {
		font-weight: bold;
	}
</style>