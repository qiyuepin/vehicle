<template>
    <div class="avatar-uploader">
        <div v-if="imgUrl" class="avatar-box">
            <img :src="imgUrl" class="avatar">
            <span class="el-upload-list__item-actions">
            <span
                    class="el-upload-list__item-preview"
                    title="预览"
                    @click.prevent="handlePreview()">
                    <i class="el-icon-zoom-in"></i>
            </span>
            <span
                    class="el-upload-list__item-delete"
                    title="删除"
                    @click.prevent="handleRemove()">
                <i class="el-icon-delete"></i>
            </span>
      </span>
        </div>
        <el-upload
                :action="attr.action"
                :accept="attr.accept"
                :multiple="attr.multiple"
                :name="attr.fileName"
                :headers="attr.headers"
                :show-file-list="attr.showFileList"
                :on-success="handleImageSuccess"
                title="上传图片"
                :before-upload="beforeImageUpload">
            <i class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        <el-dialog :modal-append-to-body="false" :append-to-body="true" :visible.sync="dialogVisible">
            <img width="100%" :src="imgUrl" alt="">
        </el-dialog>
    </div>
</template>
<script>
import defaultSettings from '@/settings.js'
import { getToken } from "@/utils/auth";
import { Message } from "element-ui";

export default {
  name: 'SingleImg',
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
    }
  },
  data() {
    return {
      attr: {
        action: process.env.VUE_APP_BASE_URL + process.env.VUE_APP_BASE_API + defaultSettings.uploadImgActive,
        accept: 'image/*',
        multiple: false,
        fileName: this.fileName,
        drag: this.drag,
        showFileList: false,
        headers: {
          'X-Access-Appid': defaultSettings.appid,
          'Authorization': 'Bearer ' + getToken()
        }
      },
      imgUrl: '',
      dialogVisible:false
    }
  },
  methods: {
    handlePreview(){
      this.dialogVisible = true;
    },
    handleRemove() {
      this.imgUrl = '';
      this.$emit('input', '');
    },
    handleImageSuccess(res, file) {
      if (res.code === 200) {
        console.log(res.data.url)
        this.imgUrl = res.data.url;
        this.$emit('input', res.data.url);
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
      const isLt2M = file.size / 1024 / 1024 < 1
      if (!isIMG) {
        this.$message.error('上传图片只能是JPG、PNG格式!')
        return false
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过2MB!')
        return false
      }
      return isIMG && isLt2M
    }
  }
}
</script>
<style >
    .avatar-uploader {
        margin: 0;
        display: inline;
        vertical-align: top;
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

    /*.avatar-uploader .el-upload-list__item-actions .el-upload-list__item-delete {*/
    /*    position: static;*/
    /*    font-size: inherit;*/
    /*    color: inherit;*/
    /*    display: inline;*/
    /*}*/

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
