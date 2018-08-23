// pages/home/home.js
import {Home} from 'home-model.js';
var home = new Home();

Page({

  /**
   * 页面的初始数据
   */
  data: {
  
  },
  onLoad:function(){ //页面初始化
    this._loadData();
  },

  _loadData:function(){
    var id = 1;
    var data = home.getBannerData(id, (res)=>{
      this.setData({
        'bannerArr':res
      })
    });
    home.getThemeData((res) => {
      this.setData({
        'themeArr': res
      })
    });
    home.getProductData((res) => {
      this.setData({
        'productArr': res
      })
    });
  },

  onProductsItemTap: function (event) {
    var id = home.getDataSet(event, 'id');
    wx.navigateTo({
      url: '../product/product?id=' + id
    });
  },

  onThemeItemTap: function (event) {
    var id = home.getDataSet(event, 'id');
    var name = home.getDataSet(event, 'name');
    wx.navigateTo({
      url: '../theme/theme?id=' + id + '&name=' + name
    });
  }
})