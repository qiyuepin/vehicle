<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col :span="8">
        <el-card class="info">
          <div class="top">
            <div class="avatar">
              <el-avatar :src="author.avatar" :size="100">
              </el-avatar>
            </div>
            <p class="name" v-text="author.nickname"></p>
            <p class="sign" v-if="author.signature" v-text="author.signature"></p>
          </div>
          <div class="left">
            <div class="left-box">
              <i class="el-icon-male" />
              <span v-text="author.gender==1?'男':author.gender==2?'女':'保密'"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-magic-stick" />
              <span v-text="author.birthday"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-message" />
              <span v-text="author.email"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-mobile" />
              <span v-text="author.phone"></span>
            </div>
            <div class="left-box">
              <i class="iconfont el-icon-qq" style="font-size: 15px"></i>
              <span v-text="author.qq"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-postcard" />
              <span v-text="author.position"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-office-building" />
              <span v-text="author.company"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-location-outline" />
              <span v-text="author.address"></span>
            </div>
            <div class="left-box">
              <i class="el-icon-link" />
              <el-link style="margin-left:5px" :href="author.link" type="primary" target="_blank" v-text="author.link"></el-link>
            </div>
            <div class="left-box">
              <i class="el-icon-crop" />
              <span v-text="author.speciality"></span>
            </div>
            <el-divider></el-divider>
            <div class="tags">
              <p>个性标签</p>
              <el-tag
                  v-for="(item,index) in author.label"
                  :key="index"
                  class="tag">
                {{ item }}
              </el-tag>
            </div>
            <el-divider></el-divider>
            <div class="remark">
              <p>个人简介</p>
              <div class="content" v-text="author.introduction"></div>
            </div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="16">
        <el-card class="content">
          <el-tabs value="first">
            <el-tab-pane label="个人介绍" name="first">
              <el-form  ref="authorForm" :model="author" :rules="authorRules" size="small" label-position="right"
                        label-width="90px"
                        style="width: 100%;">
                <el-form-item label="头像">
                  <UploadImage ref="Avatar" v-model="author.avatar"></UploadImage>
                </el-form-item>
                <el-form-item label="姓名">
                  <el-input v-model="author.name" clearable placeholder="请输入姓名"></el-input>
                </el-form-item>
                <el-form-item label="性别">
                  <el-select v-model="author.gender" style="width: 100%" placeholder="请选择性别">
                    <el-option
                            v-for="item in genders"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                    </el-option>
                  </el-select>
                </el-form-item>
                <el-form-item label="出生日期">
                  <el-date-picker
                          v-model="author.birthday"
                          type="date"
                          placeholder="选择出生日期"
                          format="yyyy-MM-dd"
                          value-format="yyyy-MM-dd"
                          style="width: 100%">
                  </el-date-picker>
                </el-form-item>
                <el-form-item label="手机号">
                  <el-input v-model="author.phone" clearable placeholder="请输入手机号"></el-input>
                </el-form-item>
                <el-form-item label="邮箱">
                  <el-input v-model="author.email" clearable placeholder="请输入邮箱"></el-input>
                </el-form-item>
                <el-form-item label="QQ">
                  <el-input v-model="author.qq" clearable placeholder="请输入QQ"></el-input>
                </el-form-item>
                <el-form-item label="住址">
                  <el-input v-model="author.address" clearable placeholder="请输入住址"></el-input>
                </el-form-item>
                <el-form-item label="公司">
                  <el-input v-model="author.company" clearable placeholder="请输入公司"></el-input>
                </el-form-item>
                <el-form-item label="职位">
                  <el-input v-model="author.position" clearable placeholder="请输入职位"></el-input>
                </el-form-item>
                <el-form-item label="特长">
                  <el-input v-model="author.speciality" clearable placeholder="请输入特长"></el-input>
                </el-form-item>
                <el-form-item label="个人网站">
                  <el-input v-model="author.link" clearable placeholder="请输入个人网站"></el-input>
                </el-form-item>
                <el-form-item label="签名">
                  <el-input v-model="author.signature" clearable placeholder="请输入签名"></el-input>
                </el-form-item>
                <el-form-item label="个人简介">
                  <el-input v-model="author.introduction" clearable placeholder="请输入个人简介"></el-input>
                </el-form-item>
                <el-form-item label="个性标签">
                  <el-popconfirm
                          title="您确定要删除吗？"
                          v-for="(item,index) in author.label"
                          :key="index"
                          icon="el-icon-info"
                          icon-color="red"
                          @confirm="deleteLabel(index)">
                    <el-tag
                            :key="index"
                            class="label"
                            slot="reference"
                            >
                      {{ item }}
                    </el-tag>
                  </el-popconfirm>
                  <el-input v-show="showInput" autofocus="true" ref="labelInput" @keyup.enter.native="changeInput" size="mini" clearable @blur="blurInput" style="width: 100px;" v-model="inputValue" placeholder="标签名"></el-input>
                  <el-button v-if="!showInput" plain size="mini" @click="addLabel">添加</el-button>
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="saveAuthor">保 存</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
            <el-tab-pane label="账号信息" name="second">
              <el-form  ref="infoForm" :model="user" :rules="infoRules" size="small" label-position="right"
                       label-width="90px"
                       style="width: 100%;">
                <el-form-item label="用户名">
                  <el-input v-model="user.username" disabled clearable placeholder="请输入2-10个字符"></el-input>
                </el-form-item>
                <el-form-item label="选择角色">
                  <el-checkbox-group v-model="user.roles">
                    <el-checkbox v-for="item in group" disabled :key="item.id" :label="item.id">{{item.title}}</el-checkbox>
                  </el-checkbox-group>
                </el-form-item>
                <el-form-item label="昵称" prop="nickname">
                  <el-input v-model="user.nickname" clearable placeholder="请输入20个以内的中文字符"></el-input>
                </el-form-item>
                <el-form-item label="手机号" prop="phone">
                  <el-input v-model="user.phone" clearable placeholder="请输入正确的手机号"></el-input>
                </el-form-item>
                <el-form-item label="邮箱" prop="email">
                  <el-input  v-model="user.email" clearable
                            placeholder="请输入正确的邮箱"></el-input>
                </el-form-item>
                <el-form-item label="个人签名">
                  <el-input v-model="user.signature" clearable
                            placeholder="请输入个人签名"></el-input>
                </el-form-item>
                <el-form-item label="用户头像" prop="avatar">
                  <UploadImage ref="Image" v-model="user.avatar"></UploadImage>
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="saveInfo">保 存</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
            <el-tab-pane label="修改密码" name="third">
              <el-form  ref="passForm" :model="pass" :rules="passRules" size="small" label-position="right"
                        label-width="90px"
                        style="width: 100%;">
                <el-form-item label="用户名">
                  <el-input v-model="user.username" disabled clearable placeholder="请输入个2-10字符"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                  <el-input clearable show-password
                            autocomplete="new-password"
                            v-model="pass.password"
                            placeholder="请输入6-18个字母和数字下划线"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="confirm_password" >
                  <el-input clearable show-password
                            autocomplete="new-password"
                            v-model="pass.confirm_password"
                            placeholder="请输入6-18个字母和数字下划线"></el-input>
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="updatePass">修改密码</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
            <el-tab-pane label="时间线" name="fourth">
              <div class="block">
                <el-timeline>
                  <el-timeline-item timestamp="2018/4/12" placement="top">
                    <el-card>
                      <h4>更新 Github 模板</h4>
                      <p>王小虎 提交于 2018/4/12 20:46</p>
                    </el-card>
                  </el-timeline-item>
                  <el-timeline-item timestamp="2018/4/3" placement="top">
                    <el-card>
                      <h4>更新 Github 模板</h4>
                      <p>王小虎 提交于 2018/4/3 20:46</p>
                    </el-card>
                  </el-timeline-item>
                  <el-timeline-item timestamp="2018/4/2" placement="top">
                    <el-card>
                      <h4>更新 Github 模板</h4>
                      <p>王小虎 提交于 2018/4/2 20:46</p>
                    </el-card>
                  </el-timeline-item>
                </el-timeline>
              </div>
            </el-tab-pane>
          </el-tabs>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import UploadImage from '@/components/Upload/SingleImage'
import { getRole} from '@/api/admin.js'
import { saveInfo,updatePass,author,updateAuthor} from '@/api/user.js'
import { validEmail, validNickname, validPassword, validPhone } from "@/utils/validate";
export default {
  name: 'Profile',
  components: {
    UploadImage
  },
  data() {
    const validateNickname = (rule, value, callback) => {
      if (!validNickname(value)) {
        callback(new Error('昵称必须是20个以内的中文字符'))
      } else {
        callback()
      }
    }
    const validatePhone = (rule, value, callback) => {
      if (!validPhone(value)) {
        callback(new Error('请输入正确的手机号'))
      } else {
        callback()
      }
    }
    const validateEmail = (rule, value, callback) => {
      if (!validEmail(value)) {
        callback(new Error('请输入正确的邮箱地址'))
      } else {
        callback()
      }
    }
    const validatePassword = (rule, value, callback) => {
      if (!validPassword(value)) {
        callback(new Error('登录密码必须是字母、数字、下划线组合，且长度6-18位'))
      } else {
        callback()
      }
    }
    const validateConfirmPassword = (rule, value, callback) => {
        if(this.pass.password!=value){
          callback(new Error('确认密码不正确'))
        }else {
          callback()
        }
    }
    return {
      group: [],
      genders:[{
        value: 1,
        label: '男'
      },{
        value: 2,
        label: '女'
      },{
        value: 3,
        label: '保密'
      }],
      author:{
        name:'',
        gender:3,
        avatar:'',
        signature:'',
        phone:'',
        email:'',
        qq:'',
        position:'',
        company:'',
        address:'',
        introduction:'',
        birthday:'',
        link:'',
        speciality:'',
        label:[]
      },
      user:{
        id:0,
        nickname:'',
        username:'',
        avatar:'',
        signature:'',
        phone:'',
        email:'',
        roles:[]
      },
      pass:{
        password:'',
        confirm_password: '',
      },
      showInput:false,
      inputValue:'',
      authorRules:{},
      infoRules: {
        nickname: [{ required: true, trigger: 'blur', validator: validateNickname }],
        phone: [{ required: true, trigger: 'blur', validator: validatePhone }],
        email: [{ required: true, trigger: 'blur', validator: validateEmail }],
        avatar: [{ required: true, trigger: 'blur', message: '请上传头像' }]
      },
      passRules: {
        password: [{ required: true, trigger: 'blur', validator: validatePassword }],
        confirm_password: [{ required: true, trigger: 'blur', validator: validateConfirmPassword }],
      },
    }
  },
  computed: {
    ...mapGetters([
       'uid',
      'name',
      'username',
      'avatar',
      'roles',
      'signature',
      'roles',
      'phone',
      'email'
    ])
  },
  created() {
    this.getRole()
  },
  mounted(){
    this.getAuthor()
    this.initUser()
  },
  methods: {
    getAuthor(){
      author().then(response => {
          if(response !== undefined){
              this.author = response
              this.$refs.Avatar.imgUrl = response.avatar
          }
      })
    },
    addLabel(){
      this.showInput = true
      this.$nextTick(() => {
        this.$refs.labelInput.focus()
      })
    },
    deleteLabel(index){
      console.log(index)
      this.author.label.splice(index,1)
    },
    blurInput(){
      this.showInput = false
    },
    changeInput(){
      this.showInput = false
      if(this.inputValue){
        this.author.label.push(this.inputValue)
        this.inputValue = ''
      }
    },
    initUser(){
      let that = this
      this.user.id = this.uid
      this.user.nickname = this.name
      this.user.username = this.username
      this.user.avatar = this.avatar
      this.user.signature = this.signature
      this.user.phone = this.phone
      this.user.email = this.email
      this.$refs.Image.imgUrl = this.avatar
      this.roles.map(function (role) {
        that.user.roles.push(Number(role))
      })
    },
    saveAuthor(){
      this.$refs.authorForm.validate(valid => {
        if (valid) {
          updateAuthor(this.author).then(_ => {
            this.getAuthor()
            this.$message({
              message: '保存成功',
              type: 'success',
              duration: 5 * 1000
            })
          })
        }
      })
    },
    getRole() {
      getRole().then(response => {
          if(response !== undefined){
              this.group = response.data
          }
      })
    },
    saveInfo(){
      this.$refs.infoForm.validate(valid => {
        if (valid) {
          saveInfo(this.user).then(_ => {
            this.$store.dispatch('user/saveInfo', this.user).then(() => {
              this.$message({
                message: '保存成功',
                type: 'success',
                duration: 5 * 1000
              })
            })
          })
        }
      })
    },
    updatePass(){
      this.$refs.passForm.validate(valid => {
        if (valid) {
          updatePass({password:this.pass.password}).then(_ => {
            this.pass.password = ''
            this.pass.confirm_password = ''
            this.$message({
              message: '修改成功',
              type: 'success',
              duration: 5 * 1000
            })
          })
        }
      })
    }
  }
}

</script>
<style scoped lang="scss">
  ::v-deep .el-card__body{
    padding: 30px 20px;
  }
  .info{
    .top{
      text-align: center;
      p{
        margin: 0;
      }
      .name{
        margin-top: 15px;
        font-size: 24px;
        font-weight: 500;
        color: #262626;
      }
      .sign{
        margin-top: 8px;
        color: #a1a1a1;
        font-size: 14px;
      }
    }
    .left{
      margin-top: 20px;
      text-align: left;
      .left-box{
        margin-left: 20px;
        font-size: 14px;
        margin-bottom: 10px;
        span{
          margin-left: 5px;
        }
      }
      .el-divider--horizontal{
        margin-bottom: 10px;
      }
      .tags{
        p{
          font-size: 14px;
          margin-top:0;
        }
        .tag{
          margin-right: 10px;
          margin-bottom: 5px;
        }
      }
      .remark{
        p{
          font-size: 14px;
          margin-top:0px;
        }
        .content{
          font-size: 13px;
          line-height:20px;
          color: #faa500;
          text-indent:2em;
        }
      }
    }
  }
  .label{
    margin-right: 5px;
    margin-bottom: 5px;
    cursor: pointer;
  }
  .label:last-child{
    margin-right: 10px;
  }
</style>
