// pages/theme/theme.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
  
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = options.id;
    var name = options.name;
    this.data.id = id;
    this.data.name = name;
    console.log(id);
    console.log(name);
  },

  onReady: function () {
    wx.setNavigationBarTitle({
      title: this.data.name,
    })
  }

})