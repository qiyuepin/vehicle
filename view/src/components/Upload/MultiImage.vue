<template>
    <div class="avatar-uploader">
            <!-- 使用element-ui自带样式 -->
            <ul class="el-upload-list el-upload-list--picture-card">
                <draggable v-model="uploadFileList" class="draggable" @end="onEnd">
                    <li v-for="(file, index) in uploadFileList" :key="index" class="avatar-box">
                        <img :src="file.url" alt="" class="avatar">
                        <span class="el-upload-list__item-actions">
                        <!-- 预览 -->
                        <span class="el-upload-list__item-preview" @click.prevent="handlePreview(file.url)" title="预览">
                          <i class="el-icon-zoom-in"></i>
                        </span>
                                <!-- 删除 -->
                        <span class="el-upload-list__item-delete" @click.prevent="handleRemove(index)" title="删除">
                          <i class="el-icon-delete"></i>
                        </span>
                      </span>
                    </li>
                </draggable>
            </ul>
            <el-upload
                    title="上传图片"
                    :limit="attr.limit"
                    :on-exceed="handleExceed"
                    :action="attr.action"
                    :accept="attr.accept"
                    :multiple="attr.multiple"
                    :name="attr.fileName"
                    :headers="attr.headers"
                    :show-file-list="attr.showFileList"
                    list-type="picture-card"
                    :file-list="attr.fileList"
                    :before-upload="beforeImageUpload"
                    :on-success="handleImageSuccess">
            <i class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
        <!-- 预览弹出层 -->
        <el-dialog :modal-append-to-body="false" :append-to-body="true" :visible.sync="dialogVisible">
            <img width="100%" :src="imgUrl" alt="">
        </el-dialog>
    </div>
</template>

<script>
import defaultSettings from "@/settings";
import { getToken } from "@/utils/auth";
import { Message } from "element-ui";
import draggable from 'vuedraggable'

export default {
  name: "MultiImage",
  components: {
    draggable,
  },
  props: {
    accept: {
      type: String,
      default: 'image/*'
    },
    fileName: {
      type: String,
      default: 'image'
    },
    drag: {
      type: Boolean,
      default: false
    },
    limit: {
      type: Number,
      default: 10
    }
  },
  created() {
    console.log(this.value)
  },
  data() {
    return {
      attr: {
        action: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + defaultSettings.uploadImgActive,
        accept: 'image/*',
        multiple: true,
        fileName: this.fileName,
        drag: this.drag,
        showFileList: false,
        limit: this.limit,
        fileList: [],
        headers: {
          'X-Access-Appid': defaultSettings.appid,
          'Authorization': 'Bearer ' + getToken()
        }
      },
      imgUrl: '',
      dialogVisible:false,
      uploadFileList:[],
      uploadFiles:[]
    }
  },
  methods: {
    init(fileList){
      this.uploadFiles = fileList
      this.uploadFileList = this.uploadFiles.map(item => {
        return {
          name: item,
          url: item
        }
      })
    },
    onEnd(){
      this.uploadFiles = this.uploadFileList.map(item => {
        return item.url
      });
      this.$emit('input', this.uploadFiles);
    },
    handleExceed(){
      this.$message.warning('最多上传'+this.limit+'张图片');
    },
    handlePreview(url){
      this.dialogVisible = true;
      this.imgUrl = url;
    },
    handleRemove(index) {
      this.uploadFileList.splice(index, 1)
      this.uploadFiles = this.uploadFileList.map(item => {
        return item.url
      });
      this.$emit('input', this.uploadFiles);
    },
    handleImageSuccess(res, file) {
      if (res.code === 200) {
        this.uploadFileList.push({name:res.data.url,url:res.data.url})
        this.uploadFiles = this.uploadFileList.map(item => {
          return item.url
        });
        this.$emit('input', this.uploadFiles);
      } else {
        Message({
          message: res.message || defaultSettings.errorMsg,
          type: 'error',
          duration: 5 * 1000
        })
      }
    },
    beforeImageUpload(file) {
      const isIMG = file.type === 'image/jpeg' || file.type === 'image/png'
      const isLt2M = file.size / 1024 / 1024 < 20
      if (!isIMG) {
        this.$message.error('上传图片只能是JPG、PNG格式!')
        return false
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过20MB!')
        return false
      }
      return isIMG && isLt2M
    }
  }
}
</script>

<style>

    .draggable{
        display: inline-block;
    }

    .el-upload--picture-card {
        border: 1px dashed #d9d9d9;
        height: 101px;
        width: 101px;
        line-height: 101px;
        margin-right: 10px;
        display: inline-block;
        background: #ffffff;
        position: relative;
    }

    .avatar-uploader .avatar-box {
        display: inline-block;
        height: 101px;
        width: 101px;
        position: relative;
        border: 1px dashed #d9d9d9;
        margin-right: 10px;
    }

    .avatar-uploader .avatar-box:hover {
        border-color: #409EFF;
    }

    .avatar-uploader :nth-child(2) {
        display: inline-block;
    }

    .avatar-uploader .el-upload-list__item-actions span {
        cursor: pointer;
        position: static;
        font-size: inherit;
        color: inherit;

    }

    .avatar-uploader .el-upload-list__item-actions span+span {
        margin-left: 15px;
    }


    .avatar-uploader .el-upload-list__item-actions:hover {
        opacity: 1;
    }

    .avatar-uploader .el-upload-list__item-actions {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        cursor: default;
        text-align: center;
        color: #fff;
        font-size: 20px;
        background-color: rgba(0, 0, 0, .5);
        transition: opacity .3s;
        opacity: 0;
    }

    .avatar-uploader .el-upload-list__item-actions:after {
        display: inline-block;
        content: "";
        height: 100%;
        vertical-align: middle;
    }

    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }

    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 100px;
        height: 100px;
        line-height: 100px;
        text-align: center;
    }

    .avatar {
        width: 100%;
        height: 100%;
    }
</style>
