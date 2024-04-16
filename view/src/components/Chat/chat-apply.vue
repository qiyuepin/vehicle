<template>
    <el-dialog
            title="添加好友"
            :modal="false"
            :show-close="false"
            :visible.sync="dialogVisible"
            :center="true"
            width="30%">
        <el-form label-position="top" label-width="80px" :model="user">
            <el-form-item label="申请说明">
                <el-input ref="desc" class="desc" :autosize="{ minRows: 3}" :maxlength="100" type="textarea" placeholder="我是..."
                          v-model="nickname"></el-input>
            </el-form-item>
            <el-form-item label="设置备注">
                <el-input ref="remark" class="remark" :maxlength="10" type="text" placeholder="备注"
                          v-model="user.nickname"></el-input>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
                <el-button size="mini" @click="dialogVisible = false">取 消</el-button>
                <el-button type="success" size="mini" @click="send">发 送</el-button>
              </span>
    </el-dialog>
</template>

<script>

export default {
  name: "chat-apply",
  data() {
    return {
      dialogVisible: false,
      user: {
        id: '',
        username: '',
        nickname: '',
        avatar: '',
        remark: ''
      },
      nickname:'我是'+this.$store.getters.name
    }
  },
  methods: {
    show(item) {
      this.dialogVisible = true
      this.user.id = item.id
      this.user.username = item.username
      this.user.nickname = item.nickname
      this.user.avatar = item.avatar
    },
    send(){
      this.$emit('send',{touid:this.user.id,remark:this.user.remark})
      this.dialogVisible = false
    }
  }
}
</script>

<style scoped lang="scss">
    ::v-deep .el-dialog--center{
        .el-dialog__header{
            .el-dialog__title{
                font-size: 16px;
            }
        }
        .el-dialog__body{
            padding:0 20px;
            .el-form-item__label{
                font-weight: normal;
                font-size: 12px;
                color: #aaaaaa;
                padding: 0;
            }
            .el-form-item__content{
                .desc{
                    font-size: 13px;
                    textarea{
                        resize:none;
                        padding: 5px 8px;
                    }
                    textarea::placeholder {
                        font-size: 13px;
                    }
                }
                .el-form-item{
                    margin-bottom: 5px;
                }
                .remark{
                    input{
                        padding: 5px 8px;
                    }
                    input::placeholder {
                        font-size: 13px;
                    }
                }
            }
        }
        .el-dialog__footer{
            margin-top: 20px;
        }
    }

    .info {
        .image {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            display: inline-block;
            vertical-align: top;
            margin-right: 10px;
        }

        .info-content {
            display: inline-block;

            .username {
                margin: 0;
                font-size: 13px;
                line-height: 20px;
            }

            .nickname {
                font-size: 12px;
                margin: 0;
                line-height: 25px;
            }
        }
    }
</style>
